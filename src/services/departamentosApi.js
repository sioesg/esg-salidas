import { CONTAPQ_ENDPOINTS, extractList, requestContpaqJson } from "./contpaqApi";

const normalizarDepartamento = (cliente) => {
  const id = cliente?.cidclienteproveedor ?? cliente?.id ?? null;
  const codigo = cliente?.ccodigocliente ?? cliente?.codigo ?? "";
  const nombre = cliente?.crazonsocial ?? cliente?.nombre ?? "";

  if (codigo === "" || nombre === "") {
    return null;
  }

  return {
    id,
    codigo: String(codigo),
    nombre: String(nombre),
  };
};

export const obtenerDepartamentos = async () => {
  try {
    const response = await requestContpaqJson(CONTAPQ_ENDPOINTS.clientesProveedores);
    const clientes = extractList(response, ["clientes"]);

    if (!Array.isArray(clientes)) {
      console.error("Respuesta de clientes/proveedores invalida", response);
      return [];
    }

    return clientes
      .map(normalizarDepartamento)
      .filter(Boolean);
  } catch (error) {
    console.error("Error cargando departamentos", error);
    return [];
  }
};
