import FormSalida from "../components/FormSalida";
import ResumenSalida from "../components/ResumenSalida";

function RegistrarSalida() {

  return (

    <div className="flex flex-col xl:flex-row gap-8">

      <FormSalida />

      <ResumenSalida />

    </div>

  );
}

export default RegistrarSalida;