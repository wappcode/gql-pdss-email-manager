<?php

namespace GPDEmailManager;

use GPDCore\Library\AbstractModule;
use GPDCore\Graphql\GPDFieldFactory;
use GPDEmailManager\Entities\EmailQueue;
use GPDEmailManager\Entities\EmailMessage;
use GPDEmailManager\Entities\EmailRecipient;
use GPDEmailManager\Graphql\TypeEmailQueueEdge;
use GPDEmailManager\Entities\EmailSenderAccount;
use GPDEmailManager\Graphql\ResolversEmailQueue;
use GPDEmailManager\Graphql\TypeEmailMessageEdge;
use GPDEmailManager\Graphql\TypeEmailRecipientEdge;
use GPDEmailManager\Graphql\ResolversEmailRecipient;
use GPDEmailManager\Graphql\TypeEmailQueueConnection;
use GPDEmailManager\Graphql\TypeEmailRecipientParams;
use GPDEmailManager\Graphql\TypeEmailMessageConnection;
use GPDEmailManager\Graphql\TypeEmailSenderAccountEdge;
use GPDEmailManager\Graphql\TypeEmailRecipientConnection;
use GPDEmailManager\Graphql\TypeEmailSenderAccountConnection;

class GPDEmailManagerModule extends AbstractModule
{

    /**
     * Sobreescribir esta propiedad para agregar seguridad a todo el módulo excepto a los campos utilizados para que las personas contesten un cuestionario 
     * Para poner seguridad en estos campos en necesario sobreescribirlos
     *
     * @var ?callable
     */
    protected $defaultProxy = null;
    function getConfig(): array
    {
        return require __DIR__ . '/../config/module.config.php';
    }
    function getServicesAndGQLTypes(): array
    {
        return [
            'invokables' => [
                TypeEmailRecipientParams::class => TypeEmailRecipientParams::class
            ],
            'factories' => [
                TypeEmailMessageEdge::class => TypeEmailMessageEdge::getFactory($this->context, EmailMessage::class),
                TypeEmailMessageConnection::class => TypeEmailMessageConnection::getFactory($this->context, TypeEmailMessageEdge::class),
                TypeEmailQueueEdge::class => TypeEmailQueueEdge::getFactory($this->context, EmailQueue::class),
                TypeEmailQueueConnection::class => TypeEmailQueueConnection::getFactory($this->context, TypeEmailQueueEdge::class),
                TypeEmailRecipientEdge::class => TypeEmailRecipientEdge::getFactory($this->context, EmailRecipient::class),
                TypeEmailRecipientConnection::class => TypeEmailRecipientConnection::getFactory($this->context, TypeEmailRecipientEdge::class),
                TypeEmailSenderAccountEdge::class => TypeEmailSenderAccountEdge::getFactory($this->context, EmailSenderAccount::class),
                TypeEmailSenderAccountConnection::class => TypeEmailSenderAccountConnection::getFactory($this->context, TypeEmailSenderAccountEdge::class),
            ],
            'aliases' => [
                TypeEmailMessageEdge::NAME => TypeEmailMessageEdge::class,
                TypeEmailMessageConnection::NAME => TypeEmailMessageConnection::class,
                TypeEmailQueueEdge::NAME => TypeEmailQueueEdge::class,
                TypeEmailQueueConnection::NAME => TypeEmailQueueConnection::class,
                TypeEmailRecipientEdge::NAME => TypeEmailRecipientEdge::class,
                TypeEmailRecipientConnection::NAME => TypeEmailRecipientConnection::class,
                TypeEmailSenderAccountEdge::NAME => TypeEmailSenderAccountEdge::class,
                TypeEmailSenderAccountConnection::NAME => TypeEmailSenderAccountConnection::class,
                TypeEmailRecipientParams::NAME => TypeEmailRecipientParams::class
            ]
        ];
    }
    function getResolvers(): array
    {
        return [
            'EmailQueue::message' => ResolversEmailQueue::getMessageResolver($proxy = null),
            'EmailQueue::senderAccount' => ResolversEmailQueue::getMessageResolver($proxy = null),
            'EmailRecipient::queue' => ResolversEmailRecipient::getQueueResolver($proxy = null),
        ];
    }
    function getQueryFields(): array
    {
        $emailMessageConnection = $this->context->getServiceManager()->get(TypeEmailMessageConnection::class);
        $emailQueueConnection = $this->context->getServiceManager()->get(TypeEmailQueueConnection::class);
        $emailRecipientConnection = $this->context->getServiceManager()->get(TypeEmailRecipientConnection::class);
        $emailSenderAccountConnection = $this->context->getServiceManager()->get(TypeEmailSenderAccountConnection::class);
        return [
            'emailMessageConnection' => GPDFieldFactory::buildFieldConnection($this->context, $emailMessageConnection, EmailMessage::class, EmailMessage::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailMessage' => GPDFieldFactory::buildFieldItem($this->context, EmailMessage::class, EmailMessage::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailQueueConnection' => GPDFieldFactory::buildFieldConnection($this->context, $emailQueueConnection, EmailQueue::class, EmailQueue::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailQueue' => GPDFieldFactory::buildFieldItem($this->context, EmailQueue::class, EmailQueue::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailRecipientConnection' => GPDFieldFactory::buildFieldConnection($this->context, $emailRecipientConnection, EmailRecipient::class, EmailRecipient::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailRecipient' => GPDFieldFactory::buildFieldItem($this->context, EmailRecipient::class, EmailRecipient::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailRecipientConnection' => GPDFieldFactory::buildFieldConnection($this->context, $emailRecipientConnection, EmailRecipient::class, EmailRecipient::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailRecipient' => GPDFieldFactory::buildFieldItem($this->context, EmailRecipient::class, EmailRecipient::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailSenderAccountConnection' => GPDFieldFactory::buildFieldConnection($this->context, $emailSenderAccountConnection, EmailSenderAccount::class, EmailSenderAccount::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'emailSenderAccount' => GPDFieldFactory::buildFieldItem($this->context, EmailSenderAccount::class, EmailSenderAccount::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
        ];
    }
    function getMutationFields(): array
    {
        return [
            'createEmailMessage' => GPDFieldFactory::buildFieldCreate($this->context, EmailMessage::class, EmailMessage::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'updateEmailMessage' => GPDFieldFactory::buildFieldUpdate($this->context, EmailMessage::class, EmailMessage::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'deleteEmailMessage' => GPDFieldFactory::buildFieldDelete($this->context, EmailMessage::class, EmailMessage::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'createEmailQueue' => GPDFieldFactory::buildFieldCreate($this->context, EmailQueue::class, EmailQueue::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'updateEmailQueue' => GPDFieldFactory::buildFieldUpdate($this->context, EmailQueue::class, EmailQueue::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'deleteEmailQueue' => GPDFieldFactory::buildFieldDelete($this->context, EmailQueue::class, EmailQueue::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'createEmailRecipient' => GPDFieldFactory::buildFieldCreate($this->context, EmailRecipient::class, EmailRecipient::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'updateEmailRecipient' => GPDFieldFactory::buildFieldUpdate($this->context, EmailRecipient::class, EmailRecipient::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'deleteEmailRecipient' => GPDFieldFactory::buildFieldDelete($this->context, EmailRecipient::class, EmailRecipient::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'createEmailSenderAccount' => GPDFieldFactory::buildFieldCreate($this->context, EmailSenderAccount::class, EmailSenderAccount::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'updateEmailSenderAccount' => GPDFieldFactory::buildFieldUpdate($this->context, EmailSenderAccount::class, EmailSenderAccount::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
            'deleteEmailSenderAccount' => GPDFieldFactory::buildFieldDelete($this->context, EmailSenderAccount::class, EmailSenderAccount::RELATIONS_MANY_TO_ONE, $this->defaultProxy),
        ];
    }
}
