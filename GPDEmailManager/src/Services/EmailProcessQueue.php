<?php


namespace GPDEmailManager\Services;

use DateTime;
use Exception;
use GPDCore\Library\IContextService;
use GPDEmailManager\Services\MailerService;
use GPDEmailManager\Entities\EmailRecipient;
use GPDEmailManager\Entities\EmailSenderAccount;
use GPDEmailManager\Library\EmialPassworEncoder;

/**
 * Send emails
 */
class EmailProcessQueue {

    const PARAM_DELIMITER_START = '|#';
    const PARAM_DELIMITER_END = '#|';

    const DEFAULT_CRON_EXECUTION_PER_HOUR = 12; // Each 5 minutes

    public static function proccessAll(IContextService $context){

        // Select only the accounts with messages to delivery
        $currentDate = new DateTime();
        $entityManager = $context->getEntityManager();
        $qb = $entityManager->createQueryBuilder()->from(EmailSenderAccount::class, 'senderAccount')
        ->innerJoin("senderAccount.queues","queue")
        ->innerJoin("queue.message", "message")
        ->innerJoin("queue.recipients", "recipient")
        ->select(array("senderAccount","queue", "message"))
        ;
        $qb->andWhere("recipient.sent = 0")
        ->andWhere("recipient.status like :status")
        ->andWhere("recipient.sendingDate >= :sendingDate")
        ->setParameter(":status", EmailRecipient::STATUS_WAITING)
        ->setParameter(":sendingDate", $currentDate->format("Y-m-d H:i"));

        $accounts = $qb->getQuery()->getResult();

        foreach($accounts as $account) {
            static::processAccount($context, $account);
        }
        


    }
    public static function sendNow(IContextService $context,  EmailRecipient $recipient) {
        $account = $recipient->getQueue()->getSenderAccount();
        $totalCanSend = static::getHowManyCanSend($context, $account);
        if(empty($totalCanSend)) {
            throw new Exception("Sending limit");
        }
        static::processRecipient($context, $recipient);
    }


    public static function processAccount(IContextService $context,  EmailSenderAccount $account) {
        // If the limit per hour has been reached do not send nothing
        $howManyCanSend =static::getHowManyCanSend($context, $account);
        $deliveriesPerHour = $account->getMaxDeliveriesPerHour();
        if (empty($howManyCanSend) || empty($deliveriesPerHour)) {
            return;
        }

        $executionsPerHour = $context->getConfig()->get("gql_email_manager__cron_execution_per_hour", static::DEFAULT_CRON_EXECUTION_PER_HOUR);
        // At least one execution per hour
        if (empty($executionsPerHour) || $executionsPerHour < 1) {
            return;
        }

        $deliveriesPerBlock = $deliveriesPerHour / $executionsPerHour;

        $deliveriesLimit = min($deliveriesPerBlock, $howManyCanSend);
        if (empty($deliveriesLimit)) {
            return;
        }
        $recipients = static::getAccountRecipients($context, $account, $deliveriesLimit);
        $count = 0;
        foreach($recipients as $recipient) {
            if($count++ >= $deliveriesLimit) {
                break;
            } else {
                static::processRecipient($context, $recipient);
            }
        }

    }
    public static function getHowManyCanSend(IContextService $context, EmailSenderAccount $account): int{
        $currentDate = new DateTime();
        $startDate = $currentDate->format('Y-m-d H:00');
        $endDate = $currentDate->format('Y-m-d H:59');
        $entityManager = $context->getEntityManager();
        $qb = $entityManager->createQueryBuilder()->from(EmailRecipient::class, 'recipient')
        ->select("COUNT(recipient.id)");
        $qb->andWhere('recipient.sent = 1')
        ->andWhere('recipient.sendingDate BETWEEN :startDate AND :endDate')
        ->setParameter(':startDate', $startDate)
        ->setParameter(':endDate', $endDate);
        $result = $qb->getQuery()->getSingleScalarResult();
        $totalSent = intval($result);
        $remaind = $account->getMaxDeliveriesPerHour() - $totalSent;

        return max(0, $remaind);
    }
    protected static function getAccountRecipients(IContextService $context, EmailSenderAccount $account, $limit=1) {
        $currentDate = new DateTime();
        $entityManager = $context->getEntityManager();
        $qb = $entityManager->createQueryBuilder()->from(EmailRecipient::class, 'recipient')
        ->innerJoin('recipient.queue', 'queue')
        ->innerJoin('queue.message', 'message')
        ->innerJoin('queue.senderAccount', 'senderAccount')
        ->select(array('recipient', 'queue','message','senderAccount'));
        
        // Here is where deliveries are selected
        $qb->andWhere("recipient.sent = 0")
        ->andWhere("recipient.status like :status")
        ->andWhere("recipient.sendingDate >= :sendingDate")
        ->setParameter(":status", EmailRecipient::STATUS_WAITING)
        ->setParameter(":sendingDate", $currentDate->format("Y-m-d H:i"));

        $qb
        ->andWhere("senderAccount.id = :senderAccountId")
        ->setParameter(":senderAccountId", $account->getId())
        ->addOrderBy('recipient.priority','DESC')
        ->addOrderBy('recipient.sendingDate','ASC')
        ->setMaxResults($limit);

        $recipients = $qb->getQuery()->getResult();
        return $recipients;
    }
    
   
   

    protected static function processRecipient(IContextService $context, EmailRecipient $recipient) {
        $entityManager = $context->getEntityManager();
        $queue = $recipient->getQueue();
        $account = $queue->getSenderAccount();
        $message = $queue->getMessage();
        $charset = $message->getChartset();
        $config = static::createConfig($context,$account, $charset);
        $bodyHtml = static::createBody($recipient);
        $subject = static::createSubject($recipient);
        $email = $recipient->getEmail();
        $name = $recipient->getName();
        $name = !empty($name) ? $name : '';
        $altBody = static::createAltBody($recipient);
        $replayTo = $queue->getReplyTo();
        $replayTo = !empty($replayTo) ?$replayTo : '';
        $replayToName = $queue->getReplyToName();
        $replayToName = !empty($replayToName) ? $replayToName  : '';
        $isProduction = $context->isProductionMode();
        $senderAddress = $queue->getSenderAddress();
        $senderName = $queue->getSenderName();
        if (empty($senderAddress)) {
            $senderAddress = $account->getEmail();
        }
        if (empty($senderName)) {
            $senderName = $account->getTitle();
        }
        $ok = MailerService::send($config,$email, $name, $subject, $bodyHtml, $senderAddress, $senderName, $altBody, $replayTo, $replayToName, $isProduction);
        $status = $ok ? EmailRecipient::STATUS_SENT  : EmailRecipient::STATUS_ERROR;
        $recipient->setSent(true)->setStatus($status);
        $entityManager->flush();
        
    }

    protected static function createConfig(IContextService $context, EmailSenderAccount $account, $charset='UTF-8') {
        $appConfig = $context->getConfig();
        $encriptPassword = $appConfig->get("gpd_email_manager__secret_key");
        $iv = $appConfig->get('gql_email_manager__iv');
        $testEmail = $appConfig->get('gql_email_manager__test_email_address');
        $password = EmialPassworEncoder::decrypt($account->getPassword(), $encriptPassword, $iv);
        $config = [

            'host' => $account->getHost(),
            'auth' => $account->getAuth(),
            'username' => $account->getUsername(),
            'password' => $password,
            'secure' => $account->getSecure(),
            'port' => $account->getPort(),
            'charset' => $charset,
            'test_email_address' =>$testEmail,

        ];
        return $config;
    }

    protected static function createBody(EmailRecipient $recipient) {
        $body = $recipient->getQueue()->getMessage()->getBody();
        $params = $recipient->getParams();
        if (empty($body)) {
            return null;
        }
        return static::adjustMailText($body, $params);
    }
    protected static function createAltBody(EmailRecipient $recipient) {
        $body = $recipient->getQueue()->getMessage()->getPlainTextBody();
        $params = $recipient->getParams();
        if (empty($body)) {
            return '';
        }
        return static::adjustMailText($body, $params);
    }

    protected static function  createSubject( EmailRecipient $recipient) {
        $subject = $recipient->getQueue()->getSubject();
        $params = $recipient->getParams();
        if (empty($subject)) {
            return null;
        }
        return static::adjustMailText($subject, $params);
    }
    public static function adjustMailText(string $text, array $params): string {
        $text = static::repalceParamsInText($text, $params);
        $text = static::removeParamsReferences($text);
        return $text;
    }

    public static function repalceParamsInText(string $text, array $params): string {

        if (empty($params)) {
            return $text;
        }
        foreach($params as $param) {
            if(!is_array($param) || count($param) !== 2) {
                continue;
            }
            $key = $param[0];
            $value = $param[1] ?? '';
            $search = static::PARAM_DELIMITER_START.$key.static::PARAM_DELIMITER_END;
            $text = str_replace($search,$value,$text);
        }
        return $text;

    }

    public static function removeParamsReferences(string $text) {
        $pattern =  "^\\".static::PARAM_DELIMITER_START."[a-zA-Z0-9-_\.]"."\\". static::PARAM_DELIMITER_END."$";
        $result = preg_replace("/{$pattern}/"," ", $text);
        return $result;
    }



}