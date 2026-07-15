import { extractList, requestJson } from "./capacitorHttpClient";

export const CONTAPQ_ENDPOINTS = {
  clientesProveedores: "http://189.206.185.236:5088/api/clientes-proveedores",
  productos: "http://189.206.185.236/api/Mty/ComercialProductos",
  existencia: (id) => `http://189.206.185.236/api/Mty/ComercialExistencia/${id}`,
  unidades: "http://189.206.185.236/api/Mty/Unidad",
  documentosFacturas: "http://189.206.185.236:5088/api/documentos/facturas",
};

export const requestContpaqJson = async (url, options = {}) => {
  return requestJson({
    url,
    method: options.method ?? "GET",
    data: options.data,
    headers: options.headers,
  });
};

export { extractList };
