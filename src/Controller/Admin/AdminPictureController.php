<?php
namespace App\Controller\Admin;

use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminPictureController
 * @package App\Controller\Admin
 * @Route("admin/picture")
 */
class AdminPictureController extends AbstractController {

    /**
     * @Route("/{id}", name="admin.picture.delete", methods={"DELETE"})
     * @param Picture $picture
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Picture $picture, Request $request) {
        // $request->getContent() = // récupère le token depuis app.js(le "body") sous forme de chaîne de caractères
        $data = json_decode($request->getContent(),true); // true = tableau associatif

        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();
            return new JsonResponse(['success' => 1]);
        }
        return new JsonResponse(['error' => 'Token invalide'],400);
    }
}