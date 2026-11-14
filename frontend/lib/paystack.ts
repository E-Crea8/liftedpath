type PaystackPopup = {
	checkout: (options: {
		accessCode: string;
		onSuccess?: (transaction: { reference?: string }) => void;
		onCancel?: () => void;
		onClose?: () => void;
		onLoad?: () => void;
		onError?: (error: unknown) => void;
	}) => void;
	resumeTransaction?: (accessCode: string) => void;
};

type PaystackPopConstructor = new () => PaystackPopup;

declare global {
	interface Window {
		PaystackPop?: PaystackPopConstructor;
	}
}

let loading: Promise<PaystackPopConstructor> | null = null;

export function loadPaystackPop(): Promise<PaystackPopConstructor> {
	if (typeof window === "undefined") {
		return Promise.reject(new Error("Paystack is only available in the browser."));
	}
	if (window.PaystackPop) {
		return Promise.resolve(window.PaystackPop);
	}
	if (loading) {
		return loading;
	}

	loading = new Promise((resolve, reject) => {
		const existing = document.querySelector<HTMLScriptElement>('script[data-liftedpath-paystack="1"]');
		if (existing) {
			existing.addEventListener("load", () => {
				if (window.PaystackPop) {
					resolve(window.PaystackPop);
					return;
				}
				reject(new Error("Paystack did not load."));
			});
			existing.addEventListener("error", () => reject(new Error("Paystack did not load.")));
			return;
		}

		const script = document.createElement("script");
		script.src = "https://js.paystack.co/v2/inline.js";
		script.async = true;
		script.dataset.liftedpathPaystack = "1";
		script.onload = () => {
			if (window.PaystackPop) {
				resolve(window.PaystackPop);
				return;
			}
			reject(new Error("Paystack did not load."));
		};
		script.onerror = () => reject(new Error("Paystack did not load."));
		document.head.appendChild(script);
	});

	return loading;
}

export async function openPaystackCheckout(options: {
	accessCode: string;
	reference: string;
	onSuccess: (reference: string) => void;
	onClose: () => void;
	onError: () => void;
}) {
	try {
		const PaystackPop = await loadPaystackPop();
		const popup = new PaystackPop();
		popup.checkout({
			accessCode: options.accessCode,
			onSuccess: (transaction) => {
				options.onSuccess(transaction.reference || options.reference);
			},
			onCancel: options.onClose,
			onClose: options.onClose,
			onError: options.onError,
		});
	} catch {
		options.onError();
	}
}
