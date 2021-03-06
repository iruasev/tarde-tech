<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\{
    Request, Response
};

class JWTLoginController extends FOSRestController
{
    /**
     * @Post("/login")
     */
    public function postLoginAction(Request $request)
    {
        $requestParam = json_decode($request->getContent());

        $user = null;

        if (isset($requestParam->email) && isset($requestParam->password)) {
            // Get the user
            $userRepo = $this->getDoctrine()->getRepository(User::class);
            $user     = $userRepo->findOneBy(['email' => $requestParam->email, 'password' => $requestParam->password]);
        }

        if (! $user) {
            return $this->view(
                ['error'       => 'User not found for credentials given.',
                 'status-code' => Response::HTTP_UNAUTHORIZED
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $jwt = $this->container->get('lexik_jwt_authentication.jwt_manager')->create($user);

        return $this->view(['token' => $jwt], Response::HTTP_OK);
    }
}
