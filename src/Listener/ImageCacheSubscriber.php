<?php

namespace App\Listener;

use App\Entity\Picture;
use App\Entity\Property;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ImageCacheSubscriber implements EventSubscriber {

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;
    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct (UploaderHelper $uploaderHelper, CacheManager $cacheManager) {

        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
    }

    public function getSubscribedEvents()
    {
        // On renvoie les évènements qu'on va écouter (quand une entité est supprimé ou modifié)
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args) {
        // cette partie est nécessaire car "preRemove" est appelé aussi pour une modif de l'entité "Option" en on veut
        // supprimer / remove() le fichier "imageFile" en cache uniquement quand on supprime une Picture
        $entity = $args->getEntity();
        if (!$entity instanceof Picture) {
            return;
        }
        $this->cacheManager->remove($this->uploaderHelper->asset($entity,'imageFile'));
    }

    public function preUpdate(PreUpdateEventArgs $args) {
        // cette partie est nécessaire car preUpdate est appelé aussi pour une modif de l'entité Option
        $entity = $args->getEntity();
        if (!$entity instanceof Picture) {
            return;
        }

        if ($entity->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity,'imageFile'));
        }
    }
}