#!/usr/bin/env bash
# Choose the theme files for a SiteGround deploy.
# changed_files with an empty range uses the last commit, then adds the PHP
# files that commit calls so a new function cannot land without its definition.

set -euo pipefail

: > /tmp/deploy_files.txt

note() {
	printf '%s\n' "$1"
}

write_mode() {
	note "mode=$1"
	if [ -n "${GITHUB_OUTPUT:-}" ]; then
		printf 'mode=%s\n' "$1" >> "$GITHUB_OUTPUT"
	fi
}

is_theme_file() {
	case "$1" in
		frontend/*|.github/*|README.md|AGENTS.md|CLAUDE.md)
			return 1
			;;
	esac
	case "$1" in
		*.php|*.css|*.js|*.html|*.json|*.svg|*.woff2|*.txt|*.png)
			[ -f "$1" ]
			;;
		*)
			return 1
			;;
	esac
}

add_path() {
	local path="$1"
	path="${path#./}"
	if is_theme_file "$path" && ! grep -Fxq "$path" /tmp/deploy_files.txt; then
		printf '%s\n' "$path" >> /tmp/deploy_files.txt
	fi
}

add_requires() {
	local file="$1"
	local dir quoted rel
	[ -f "$file" ] || return 0
	dir=$(dirname "$file")

	while IFS= read -r quoted; do
		[ -n "$quoted" ] || continue
		rel=${quoted#\'}
		rel=${rel#\"}
		rel=${rel%\'}
		rel=${rel%\"}
		rel=${rel#/}
		add_path "$rel"
	done <<EOF
$(grep -oE "get_template_directory\\(\\)[[:space:]]*\\.[[:space:]]*['\"][^'\"]+['\"]" "$file" | grep -oE "['\"][^'\"]+['\"]" || true)
EOF

	while IFS= read -r quoted; do
		[ -n "$quoted" ] || continue
		rel=${quoted#\'}
		rel=${rel#\"}
		rel=${rel%\'}
		rel=${rel%\"}
		case "$rel" in
			/*) continue ;;
		esac
		add_path "$dir/$rel"
	done <<EOF
$(grep -oE "__DIR__[[:space:]]*\\.[[:space:]]*['\"][^'\"]+['\"]" "$file" | grep -oE "['\"][^'\"]+['\"]" || true)
EOF
}

add_called_definitions() {
	local file="$1"
	local name def
	[ -f "$file" ] || return 0

	while IFS= read -r name; do
		[ -n "$name" ] || continue
		def=$(grep -lE "^function[[:space:]]+${name}[[:space:]]*\\(" functions.php inc/*.php blocks/*/*.php 2>/dev/null | head -n 1 || true)
		if [ -n "$def" ]; then
			add_path "$def"
		fi
	done <<EOF
$(grep -oE 'liftedpath_[a-z0-9_]+[[:space:]]*\(' "$file" | sed 's/[[:space:]]*(//' | sort -u || true)
EOF
}

expand_dependencies() {
	local file
	cp /tmp/deploy_files.txt /tmp/deploy_seed.txt

	while IFS= read -r file; do
		[ -n "$file" ] || continue
		case "$file" in
			*.php)
				add_requires "$file"
				add_called_definitions "$file"
				case "$file" in
					blocks/*/render.php)
						add_path "${file%/render.php}/block.json"
						;;
				esac
				;;
		esac
	done < /tmp/deploy_seed.txt

	cp /tmp/deploy_files.txt /tmp/deploy_hop.txt
	while IFS= read -r file; do
		case "$file" in
			*.php) add_requires "$file" ;;
		esac
	done < /tmp/deploy_hop.txt
}

theme_paths() {
	grep -E '\.(php|css|js|html|json|svg|woff2|txt|png)$' \
		| grep -v -E '^(frontend/|\.github/)' \
		| grep -v -E '^(README\.md|AGENTS\.md|CLAUDE\.md)$' \
		|| true
}

if [ "${DEPLOY_MODE:-changed_files}" = "changed_files" ]; then
	if [ -n "${COMMIT_RANGE:-}" ]; then
		note "Detecting theme files in range: $COMMIT_RANGE"
		git diff --name-only --diff-filter=ACMR "$COMMIT_RANGE" | theme_paths > /tmp/deploy_files.txt
	else
		if ! git rev-parse --verify HEAD~1 >/dev/null 2>&1; then
			note "This checkout has no parent commit, so the last commit cannot be selected."
			exit 1
		fi
		note "Selecting theme files changed in the last commit."
		git diff --name-only --diff-filter=ACMR HEAD~1 HEAD | theme_paths > /tmp/deploy_files.txt
	fi
	expand_dependencies
	if [ ! -s /tmp/deploy_files.txt ]; then
		write_mode skip
		note "No theme files in that commit. frontend/ is left for Vercel."
		exit 0
	fi
	write_mode selective
elif [ "${DEPLOY_MODE:-}" = "specific_files" ]; then
	printf '%s\n' "${SPECIFIC_FILES:-}" | tr ',' '\n' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//' | sed '/^$/d' > /tmp/deploy_files.txt
	if [ ! -s /tmp/deploy_files.txt ]; then
		note "specific_files mode needs at least one theme path."
		exit 1
	fi
	expand_dependencies
	write_mode selective
else
	write_mode full
fi

if [ -s /tmp/deploy_files.txt ]; then
	note "Files selected:"
	cat /tmp/deploy_files.txt
	while IFS= read -r path; do
		case "$path" in
			frontend/*|.github/*)
				note "Refusing to deploy ${path} to SiteGround."
				exit 1
				;;
		esac
		if [ ! -e "$path" ]; then
			note "Missing path: ${path}"
			exit 1
		fi
	done < /tmp/deploy_files.txt
fi
