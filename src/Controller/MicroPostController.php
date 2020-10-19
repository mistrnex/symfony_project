<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
//use Symfony\Component\Security\Core\Security;

/**
 * Class MicroPostController
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
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
                                AuthorizationCheckerInterface $authorizationChecker
//                                $securitySecurity
    )
    {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
//        $this->security = $security;
    }

    /**
     * @Route("/", name="micro_post_index")
     * @return Response
     */
    public function index(): Response
    {
        $html = $this->render('micro-post/index.html.twig',
            [
                'posts' => $this->microPostRepository->findBy([], ['time' => 'DESC']),
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
            $this->render('micro-post/add.html.twig',
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
//        $user = $tokenStorage->getToken()->getUser();

//        $user = $this->security->getUser();

        $microPost = new MicroPost();
        $microPost->setTime(new DateTime());
//        $microPost->setUser($user);

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }

        return new Response(
            $this->render('micro-post/add.html.twig',
                ['form' => $form->createView()]
            )
        );
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
            $this->render('micro-post/post.html.twig',
                ['post' => $post]
            )
        );

    }

}