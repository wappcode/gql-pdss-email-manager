<?php

namespace GPDEmailManager\Library;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDEmailManager\Entities\EmailRecipient;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ImportRecipientsFromExcel
{
    /**
     * 
     *
     * @var IContextService
     */
    private $context;


    /**
     *
     * @var int
     */
    private $priority;

    /**
     *      
     * @var EmailQueue
     */
    private $emailQueue;

    /**
     * @var DateTimeInterface
     */
    private $sendingDate;

    /**
     * @var string
     */
    private $status;

    public static function import(IContextService $context, string $filePath, EmailQueue $emailQueue, int $priority, DateTimeInterface $sendingDate, string $status)
    {
        $instance = new ImportRecipientsFromExcel($context, $emailQueue, $priority, $sendingDate, $status);
        $instance->createRecipients($filePath);
    }

    private function __construct(IContextService $context, EmailQueue $emailQueue, int $priority, DateTimeInterface $sendingDate, string $status)
    {
        $this->context = $context;
        $this->priority = $priority;
        $this->emailQueue = $emailQueue;
        $this->sendingDate = $sendingDate;
        $this->status = $status;
    }

    private function createRecipients(string $filePath)
    {
        $sheet = $this->getSheet($filePath);
        $worksheet = $sheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5
        $titles = [];
        $titleRow = 1;
        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
            $cell = $worksheet->getCell([$col, $titleRow]);
            $val = $cell->getValue();
            $titles[] = trim($val);
        }
        $lowerTitles = array_map('strtolower', $titles);
        $emailIndex = array_search('email', $lowerTitles); // valid values (email, Email, EMAIL)
        $nameIndex = array_search('name', $lowerTitles); // valid values (name, Name, NAME)
        $ownerCodeIndex = array_search('ownercode', $lowerTitles); // valid values (ownercode, OwnerCode, OWNERCODE, ownerCode)
        if ($emailIndex === false) {
            throw new Exception('Email column is required');
        }
        if ($nameIndex === false) {
            throw new Exception('Email column is required');
        }
        $emailColumn = $emailIndex + 1;
        $nameColumn = $nameIndex === false ? 0 : $nameIndex + 1;
        $owneerCodeColumn = $ownerCodeIndex === false ? 0 : $ownerCodeIndex + 1;
        $entityManager = $this->context->getEntityManager();
        $entityManager->beginTransaction();
        try {
            for ($row = 2; $row <= $highestRow; ++$row) {
                $email = $worksheet->getCell([$emailColumn, $row])->getValue();
                $name =  $worksheet->getCell([$nameColumn, $row])->getValue();
                $email = trim($email);
                $name = trim($name);
                $ownerCode = empty($owneerCodeColumn) ? '' : $worksheet->getCell([$owneerCodeColumn, $row])->getValue();
                if (empty($email) || empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                if (empty($ownerCode)) {
                    $ownerCode = null;
                }
                $params = [];
                for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                    $titleIndex = $col - 1;
                    $title = $titles[$titleIndex] ?? '';
                    if (empty($title)) {
                        continue;
                    }
                    $val = $worksheet->getCell([$col, $row])->getValue();
                    $params[] = [$title, $val];
                }
                $this->createRecipient($email, $name, $params, $ownerCode);
            }
            $entityManager->flush();
            $entityManager->commit();
        } catch (Exception $e) {
            $entityManager->rollback();
            throw $e;
        }
    }

    private function getSheet(string $filePath): Spreadsheet
    {
        $testFormats = [
            IOFactory::READER_XLS,
            IOFactory::READER_XLSX,
            IOFactory::READER_CSV,
            IOFactory::READER_ODS,

        ];

        try {
            /**  Identify the type of $inputFileName (xls,xlsx,csv,ods,etc) **/
            $inputFileType = IOFactory::identify($filePath, $testFormats);

            /**  Create a new Reader of the type that has been identified  **/
            $reader = IOFactory::createReader($inputFileType);

            $reader->setReadDataOnly(true);

            /**  Load $inputFileName to a Spreadsheet Object  **/
            $spreadsheet = $reader->load($filePath);
            return $spreadsheet;
        } catch (Exception $e) {
            throw new Exception('Invalid file type valid file types are xls, xlsx, csv, ods');
        }
    }


    private function createRecipient(string $email, ?string $name, ?array $params, ?string $ownerCode): void
    {
        $sendingDate = new DateTimeImmutable($this->sendingDate->format('c'));
        $recipient = new EmailRecipient();
        $recipient->setEmail($email)
            ->setName($name)
            ->setPriority($this->priority)
            ->setQueue($this->emailQueue)
            ->setParams($params)
            ->setOwnerCode($ownerCode)
            ->setSendingDate($sendingDate)
            ->setStatus($this->status);

        $this->context->getEntityManager()->persist($recipient);
        $this->context->getEntityManager()->flush();
    }
}
