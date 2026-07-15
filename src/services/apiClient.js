import { getAuthToken } from "./authToken";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? "";

export const requestJson = async (endpoint, options = {}) => {
  const token = getAuthToken();
  const body = options.body;

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...options,
    headers: {
      Accept: "application/json",
      ...(!(body instanceof FormData) && body !== undefined
        ? { "Content-Type": "application/json" }
        : {}),
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...options.headers,
    },
  });

  if (!response.ok) {
    const errorData = await response.json().catch(() => ({}));
    const error = new Error(
      errorData.message || "No fue posible completar la solicitud."
    );

    error.status = response.status;
    error.data = errorData;

    throw error;
  }

  if (response.status === 204) {
    return null;
  }

  return response.json().catch(() => null);
};
