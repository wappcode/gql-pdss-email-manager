<?php

use Doctrine\ORM\EntityManager;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDEmailManager\Entities\EmailRecipient;
use GPDEmailManager\Library\ImportRecipientsFromExcel;
use PHPUnit\Framework\TestCase;

class ImportRecipientsFromExcelTest extends TestCase
{


    public function testImport()
    {

        $filepath = __DIR__ . '/../assets/test-email-import.xlsx';
        /** @var IContextService */
        global $context;

        /** @var EntityManager */
        $entityManager = $context->getEntityManager();
        $queue = $entityManager->find(EmailQueue::class, "qdo5541528b2e4f88ea9697abf2316833fb");
        $date = new DateTime();

        ImportRecipientsFromExcel::import($context, $filepath, $queue, 0, $date, EmailRecipient::STATUS_PAUSE);
        // solo verifica que no haya errores o excepciones que bloqueen el test
        $this->assertTrue(true, "Probando test import sin owner code");
    }
}
