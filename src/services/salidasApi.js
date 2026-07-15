import { CONTAPQ_ENDPOINTS, requestContpaqJson } from "./contpaqApi";

export const obtenerSalidas = async (params = {}) => {
  void params;
  return { data: [], meta: null };
};

export const obtenerSalida = async (folio) => {
  void folio;
  return null;
};

export const registrarSalida = async (payload) => {
  const payloadContpaq = {
    concepto: "102",
    serie: "CONSUMOS",
    codigoClienteProveedor: payload.cliente_codigo,
    referencia: payload.referencia || "",
    codigoAgente: "",
    observaciones: payload.observaciones || "",
    movimientos: payload.productos.map((producto) => {
      const movimiento = {
        codProd: producto.codigo,
        cantidad: Number(producto.cantidad),
        precio: 0.00,
        codAlmacen: "1",
        observaciones: producto.observaciones || "",
      };

      if (payload.unidad_vehicular !== null && payload.unidad_vehicular !== undefined && payload.unidad_vehicular !== "") {
        movimiento.unidad = Number(payload.unidad_vehicular);
      }

      return movimiento;
    }),
  };

  const response = await requestContpaqJson(CONTAPQ_ENDPOINTS.documentosFacturas, {
    method: "POST",
    data: payloadContpaq,
  });

  return {
    message: "Salida registrada correctamente.",
    data: {
      folio_contpaq: response?.folio ?? response?.numero ?? response?.folioDocumento ?? null,
      contpaq_documento_id: response?.idDocumento ?? response?.id ?? response?.documentoId ?? null,
      respuesta_contpaq: response,
      payload_contpaq: payloadContpaq,
    },
  };
};
