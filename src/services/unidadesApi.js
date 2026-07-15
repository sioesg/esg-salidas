import { CONTAPQ_ENDPOINTS, extractList, requestContpaqJson } from "./contpaqApi";

const normalizarUnidad = (unidad) => {
  const numero = unidad?.numeroEconomico ?? unidad?.numero ?? null;
  const id = Number(numero);

  if (!Number.isFinite(id) || id <= 0) {
    return null;
  }

  const tipo = String(unidad?.tipo ?? "").trim();
  const placas = String(unidad?.placas ?? "").trim();

  return {
    id,
    numero: String(numero),
    nombre: `Unidad ${numero}${tipo ? ` - ${tipo}` : ""}`,
    contpaq_id: unidad?.idunidad ?? unidad?.id ?? null,
    tipo,
    marca: String(unidad?.marca ?? "").trim(),
    modelo: String(unidad?.modelo ?? "").trim(),
    placas,
  };
};

export const obtenerUnidades = async () => {
  try {
    const response = await requestContpaqJson(CONTAPQ_ENDPOINTS.unidades);
    const unidades = extractList(response, ["unidades"]);

    if (!Array.isArray(unidades)) {
      console.error("Respuesta de unidades vehiculares invalida", response);
      return [];
    }

    return unidades
      .map(normalizarUnidad)
      .filter(Boolean)
      .sort((a, b) => a.id - b.id);
  } catch (error) {
    console.error("Error cargando unidades vehiculares", error);
    return [];
  }
};
