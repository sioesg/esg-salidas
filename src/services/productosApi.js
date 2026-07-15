import { CONTAPQ_ENDPOINTS, extractList, requestContpaqJson } from "./contpaqApi";

let productosCache = [];

const toNumber = (value) => {
  if (typeof value === "number") {
    return Number.isFinite(value) ? value : null;
  }

  if (typeof value === "string" && value.trim() !== "") {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : null;
  }

  return null;
};

const normalizarProducto = (producto) => {
  const id = producto?.cidproducto ?? producto?.id ?? producto?.ID ?? null;
  const codigo = producto?.ccodigoproducto ?? producto?.codigo ?? producto?.Codigo ?? "";
  const nombre = producto?.cnombreproducto ?? producto?.nombre ?? producto?.Nombre ?? "";
  const tipo = producto?.ctipoproducto ?? producto?.tipo ?? null;

  if (id === null || codigo === "" || nombre === "") {
    return null;
  }

  return {
    id,
    codigo: String(codigo),
    nombre: String(nombre),
    tipo,
  };
};

export const extraerExistencia = (data) => {
  const directa = toNumber(data);

  if (directa !== null) {
    return directa;
  }

  if (Array.isArray(data)) {
    for (const item of data) {
      const value = extraerExistencia(item);
      if (value !== null) {
        return value;
      }
    }

    return null;
  }

  if (data && typeof data === "object") {
    for (const key of ["existencia", "Existencia", "EXISTENCIA", "cantidad", "Cantidad", "value", "Value"]) {
      const value = toNumber(data[key]);

      if (value !== null) {
        return value;
      }
    }
  }

  return null;
};

export const obtenerProductos = async () => {
  try {
    const response = await requestContpaqJson(CONTAPQ_ENDPOINTS.productos);
    const productos = extractList(response, ["productos"]);

    if (!Array.isArray(productos)) {
      console.error("Respuesta de productos invalida", response);
      productosCache = [];
      return [];
    }

    productosCache = productos
      .map(normalizarProducto)
      .filter(Boolean);

    return productosCache;
  } catch (error) {
    console.error("Error cargando productos", error);
    productosCache = [];
    return [];
  }
};

export const obtenerProducto = async (id) => {
  const productos = Array.isArray(productosCache) && productosCache.length > 0
    ? productosCache
    : await obtenerProductos();
  const producto = productos.find((item) => String(item.id) === String(id));

  if (!producto) {
    throw new Error("Producto no encontrado en CONTPAQi");
  }

  const existenciaResponse = await requestContpaqJson(CONTAPQ_ENDPOINTS.existencia(id));
  const existencia = extraerExistencia(existenciaResponse);

  if (existencia === null) {
    console.error("Respuesta existencia invalida", existenciaResponse);
    throw new Error("No fue posible interpretar la existencia del producto");
  }

  return {
    ...producto,
    existencia,
  };
};

export const productosApi = {
  obtenerProductos,
  obtenerProducto,
};
