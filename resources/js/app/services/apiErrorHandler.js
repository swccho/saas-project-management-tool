import { toast } from 'vue-sonner';

/**
 * Parse API error response and return structured error for forms.
 * Optionally shows a toast for non-form errors.
 *
 * @param {import('axios').AxiosError} error - Axios error object
 * @param {Object} options - Options
 * @param {boolean} options.showToast - Whether to show toast (default: true for 4xx/5xx)
 * @param {boolean} options.suppressToast - Skip toast for validation (422) when form handles it
 * @returns {{ message: string, errors?: Record<string, string[]>, status?: number }}
 */
export function handleApiError(error, options = {}) {
  const { showToast = true, suppressToast = false } = options;

  const response = error?.response;
  const status = response?.status;
  const data = response?.data;

  const message = data?.message || error?.message || 'Something went wrong. Please try again.';
  const errors = data?.errors || null;

  if (showToast && !suppressToast) {
    if (status === 422 && errors) {
      const firstError = Object.values(errors)[0];
      const firstMessage = Array.isArray(firstError) ? firstError[0] : firstError;
      toast.error(firstMessage || message);
    } else if (status && status >= 400) {
      toast.error(message);
    }
  }

  return { message, errors, status };
}

/**
 * Show success toast.
 */
export function showSuccessToast(message) {
  toast.success(message);
}

/**
 * Show error toast.
 */
export function showErrorToast(message) {
  toast.error(message);
}
