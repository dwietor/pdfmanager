<?php

namespace DWietor\Bundle\PmanagerBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;

use DWietor\Bundle\PmanagerBundle\Entity\Manager\PDFTempalteActivityManager;
use DWietor\Bundle\PmanagerBundle\Entity\Manager\PDFTemplateOwnerManager;

class EntityListener
{
    /**
     * @var EmailOwnerManager
     */
    protected $emailOwnerManager;

    /**
     * @var EmailActivityManager
     */
    protected $emailActivityManager;

    public function __construct(
        PDFTemplateOwnerManager $emailOwnerManager,
        PDFTempalteActivityManager $emailActivityManager
    ) {
        $this->emailOwnerManager    = $emailOwnerManager;
        $this->emailActivityManager = $emailActivityManager;
    }

    /**
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $this->emailOwnerManager->handleOnFlush($event);
        $this->emailActivityManager->handleOnFlush($event);
    }
}
