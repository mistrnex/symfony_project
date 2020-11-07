<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

//use Symfony\Component\Security\Core\Security;

/**
 * Class MicroPostController
 * @Route("/micro-post")
 */
class MicroPostController
{
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Environment
     */
    private $twig;

//    private $security;

    /**
     * MicroPostController constructor.
     * @param MicroPostRepository $microPostRepository
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Security $security
     */
    public function __construct(MicroPostRepository $microPostRepository, FormFactoryInterface $formFactory,
                                EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag,
                                AuthorizationCheckerInterface $authorizationChecker, Environment $twig
//                                $securitySecurity
    )
    {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
        $this->authorizationChecker = $authorizationChecker;
//        $this->security = $security;
    }

    /**
     * @Route("/", name="micro_post_index")
     *
     * @param TokenStorageInterface $tokenStorage
     * @param UserRepository $userRepository
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(TokenStorageInterface $tokenStorage, UserRepository $userRepository): Response
    {
        $currentUser = $tokenStorage->getToken()
            ->getUser();

        $usersToFollow = [];

        // if is current user authenticated she/he sees just posts from people who she/he is following
        if ($currentUser instanceof User) {
            $posts = $this->microPostRepository->findAllByUsers($currentUser->getFollowing());

            $usersToFollow = count($posts) === 0 ? $userRepository->findAllWithMoreThan5PostsExceptUser($currentUser) : [];
        } else {
            $posts =  $this->microPostRepository->findBy(
                [],
                ['time' => 'DESC']
            );
        }


        $html = $this->twig->render('micro-post/index.html.twig',
            [
                'posts' => $posts,
                'usersToFollow' => $usersToFollow
            ]
        );
        return new Response($html);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @Security("is_granted('edit', microPost)", message="Access denied")
     * @param MicroPost $microPost
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(MicroPost $microPost, Request $request, AuthorizationCheckerInterface $authorizationChecker)
    {
//        $this->denyAccessUnlessGranted('edit', $microPost);

//        if (!$authorizationChecker->isGranted('edit', $microPost)) {
//            throw new UnauthorizedHttpException();
//        }

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

//        $microPost->setTime(new DateTime('2022-03-02'));

        if ($form->isSubmitted() && $form->isValid()) {
//            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig',
                ['form' => $form->createView()]
            )
        );

    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @Security("is_granted('delete', microPost)", message="Access denied")
     * @param MicroPost $microPost
     * @return RedirectResponse
     */
    public function delete(MicroPost $microPost): RedirectResponse
    {
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Micro post was deleted!');

        return new RedirectResponse(
            $this->router->generate('micro_post_index')
        );

    }

    /**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @return RedirectResponse|Response
     */
    public function add(Request $request, TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();

//        $user = $this->security->getUser();

        $microPost = new MicroPost();
//        $microPost->setTime(new DateTime());
        $microPost->setUser($user);

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig',
                ['form' => $form->createView()]
            )
        );
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     */
    public function userPosts(User $userWithPosts)
    {
        $html = $this->twig->render('micro-post/user-posts.html.twig',
            [
                'posts' => $this->microPostRepository->findBy(
                    ['user' => $userWithPosts],
                    ['time' => 'DESC']
                ),
                'user' => $userWithPosts,
//            'posts' => $userWithPosts->getPosts()
            ]
        );

        return new Response($html);

    }

    /**
     * @Route("/{id}", name="micro_post_post")
     * @param MicroPost $post
     * @return Response
     */
    public function post(MicroPost $post)
    {
//        $post = $this->microPostRepository->find($id);

        return new Response(
            $this->twig->render('micro-post/post.html.twig',
                ['post' => $post]
            )
        );

    }

}