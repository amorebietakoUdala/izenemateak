<?php

namespace App\Service;

use App\Entity\Registration;
use League\Csv\Writer;
use League\Csv\CharsetConverter;
use Symfony\Component\Config\FileLocator;

class CsvGeneratorService {

   const INSTITUTION='AMOREBIE';
//    const VALID_HEADERS = [
//       'Nombre',
//       'Apellido1',
//       'Apellido2',
//       'Dni',
//       'Importe',
//       'Cuenta_Corriente',
//       'Nombre_Titular',
//       'Apellido1_Titular',
//       'Apellido2_Titular',
//       'Dni_Titular',
//       'Referencia_Externa',
//       'Presupuesto',
//       'Institucion',
//       'Tipo_Ingreso',
//       'Tributo',
//       'Fecha_Inicio_Pago',
//       'Fecha_Limite_Pago',
//       'Referencia_C19',
//       'Cuerpo1',
//       'Cuerpo2',
//       'Cuerpo3',
//       'Cuerpo4',
//       'Cuerpo5',
//       'Cuerpo6',
//       'Cuerpo7',
//   ];   

   private $projectDir = null;

   public function __construct(string $projectDir){
      $this->projectDir = $projectDir;
   }

   public function createRow(Registration $registration, $concepts) {
      $row = [];
      $row[] = $registration->getName();
      $row[] = $registration->getSurname1();
      $row[] = $registration->getSurname2();
      $row[] = $registration->getDni();
      $cost = $registration->getActivity()->getCost();
      if ($registration->getSubscriber() && $registration->getActivity()->getCostForSubscribers() !== null) {
         $cost = $registration->getActivity()->getCostForSubscribers();
      }
      $row[] = $cost;
      $row[] = $registration->getPaymentIBANAccount();
      $row[] = $registration->getPaymentName();
      $row[] = $registration->getPaymentSurname1();
      $row[] = $registration->getPaymentSurname2();
      $row[] = $registration->getPaymentDni();
      $row[] = $registration->getId();
      $row[] = date("Y");
      $row[] = $concepts[$registration->getActivity()->getAccountingConcept()]['concept']['entity'];
      $row[] = $concepts[$registration->getActivity()->getAccountingConcept()]['tipoIngreso']['codigo'];
      $row[] = $concepts[$registration->getActivity()->getAccountingConcept()]['concept']['suffix'];
      $row[] = date('d/m/Y');
      $row[] = date('d/m/Y', strtotime("+1 month"));
      $row[] = '';
      $row[] = $registration->getActivity()->getNameEs();

      return $row;
   }

   /**
    * @param Registration[] $registrations
    */
   public function createCsv(array $registrations, $concepts) {
      $fileLocator = new FileLocator($this->projectDir.'/src/Resources');
      $filename = $fileLocator->locate('template.csv', null, true);
      $content = file_get_contents($filename);
      $writer = Writer::createFromString($content);
      $writer->setDelimiter(';');
      CharsetConverter::addTo($writer, 'UTF-8', 'Windows-1252');
      foreach ($registrations as $registration) {
         $row = $this->createRow($registration, $concepts);
         $writer->insertOne($row);
      }
      $csv = $writer->toString();

      return $csv;
   }

}