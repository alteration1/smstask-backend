<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

/**
 * @author Rossana Pencheva <rossana.ruseva@gmail.com>
 */
class RegisterController extends AbstractController
{
   /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/api/register/user", name="app_register_user", methods={"POST"})
     */
    public function registerUser(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        if(!isset($content['email']) && !$content['email'] && !isset($content['phone']) && !$content['phone'] && !isset($content['password']) && !$content['password']){
            throw new NotFoundHttpException('Data not found.');
        }
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $content['email']]);
        if($user){
            return new JsonResponse(['message' => "You are already registered."], 
            JsonResponse::HTTP_OK);
        }else{
            $user = new User($content['email'], $content['phone'], $content['password']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return new JsonResponse(['message' => "Your phone has been successfully validated."], 
        JsonResponse::HTTP_CREATED);
        }
        
    }
}