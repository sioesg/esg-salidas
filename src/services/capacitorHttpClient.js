import { CapacitorHttp } from "@capacitor/core";

const DEFAULT_HEADERS = {
  Accept: "application/json",
};

export const parseResponseData = (data) => {
  if (typeof data !== "string") {
    return data;
  }

  const trimmed = data.trim();

  if (trimmed === "") {
    return null;
  }

  try {
    return JSON.parse(trimmed);
  } catch {
    return data;
  }
};

export const requestJson = async ({ method = "GET", url, data, headers = {} }) => {
  const options = {
    url,
    headers: {
      ...DEFAULT_HEADERS,
      ...headers,
    },
  };

  if (data !== undefined) {
    options.data = data;
    options.headers["Content-Type"] = "application/json";
  }

  const response = method === "POST"
    ? await CapacitorHttp.post(options)
    : await CapacitorHttp.get(options);

  const parsedData = parseResponseData(response.data);
  const status = Number(response.status);

  if (!Number.isFinite(status) || status < 200 || status >= 300) {
    const error = new Error(
      parsedData?.message
      || parsedData?.mensaje
      || `CONTPAQi respondio con estatus ${response.status}`
    );

    error.status = response.status;
    error.data = parsedData;
    throw error;
  }

  return parsedData;
};

export const extractList = (data, keys = []) => {
  const parsedData = parseResponseData(data);

  if (Array.isArray(parsedData)) {
    return parsedData;
  }

  for (const key of keys) {
    if (Array.isArray(parsedData?.[key])) {
      return parsedData[key];
    }
  }

  for (const key of ["value", "data", "items", "result", "results"]) {
    if (Array.isArray(parsedData?.[key])) {
      return parsedData[key];
    }
  }

  return [];
};
