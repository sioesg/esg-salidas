import { useState } from "react";
import { Capacitor, registerPlugin } from "@capacitor/core";

const CapacitorBarcodeScanner = registerPlugin("CapacitorBarcodeScanner");
const BarcodeScanner = registerPlugin("BarcodeScanner");

const extractScannedValue = (result) => {
  if (!result || typeof result !== "object") {
    return "";
  }

  return String(
    result.ScanResult
    ?? result.scanResult
    ?? result.content
    ?? result.text
    ?? result.value
    ?? result.rawValue
    ?? result.barcode?.rawValue
    ?? result.barcodes?.[0]?.rawValue
    ?? ""
  );
};

const requestCameraPermission = async (scanner) => {
  for (const method of ["requestPermissions", "requestPermission"]) {
    try {
      const response = await scanner[method]();
      const camera = response?.camera ?? response?.granted ?? response?.permission;

      if (camera === false || camera === "denied") {
        throw new Error("No hay permiso de camara.");
      }

      return;
    } catch (error) {
      if (String(error?.message ?? "").toLowerCase().includes("permission")) {
        throw error;
      }
    }
  }
};

const scanWithAvailableMethod = async (scanner) => {
  const attempts = [
    () => scanner.scanBarcode({}),
    () => scanner.scan({}),
    () => scanner.startScan({}),
  ];

  let lastError = null;

  for (const attempt of attempts) {
    try {
      return await attempt();
    } catch (error) {
      lastError = error;
    }
  }

  throw lastError ?? new Error("No fue posible iniciar el escaner.");
};

function PruebaEscaner() {
  const [codigoEscaneado, setCodigoEscaneado] = useState("");
  const [errorEscaner, setErrorEscaner] = useState("");
  const [escaneando, setEscaneando] = useState(false);

  const probarEscaner = async () => {
    setCodigoEscaneado("");
    setErrorEscaner("");

    if (!Capacitor.isNativePlatform()) {
      setErrorEscaner("El escaner solo esta disponible en Android.");
      return;
    }

    setEscaneando(true);

    try {
      const scanner = Capacitor.isPluginAvailable("CapacitorBarcodeScanner")
        ? CapacitorBarcodeScanner
        : BarcodeScanner;

      await requestCameraPermission(scanner);

      const result = await scanWithAvailableMethod(scanner);
      const value = extractScannedValue(result);

      if (!value) {
        setErrorEscaner("Escaneo cancelado o sin resultado.");
        return;
      }

      setCodigoEscaneado(value);
    } catch (error) {
      console.error("Error probando escaner de codigo de barras", error);
      setErrorEscaner(error?.message || "No fue posible abrir el escaner.");
    } finally {
      setEscaneando(false);
    }
  };

  return (
    <div className="mt-3">
      <button
        type="button"
        onClick={probarEscaner}
        disabled={escaneando}
        className="h-14 px-6 rounded-lg font-medium transition bg-slate-900 text-white hover:bg-slate-700 disabled:opacity-60"
      >
        {escaneando ? "Escaneando..." : "Probar escaner"}
      </button>

      {codigoEscaneado && (
        <p className="mt-2 text-sm text-gray-700">
          <span className="font-semibold">Codigo escaneado:</span> {codigoEscaneado}
        </p>
      )}

      {errorEscaner && (
        <p className="mt-2 text-sm text-red-600">
          {errorEscaner}
        </p>
      )}
    </div>
  );
}

export default PruebaEscaner;
