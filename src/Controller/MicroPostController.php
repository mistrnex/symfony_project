<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

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
     * MicroPostController constructor.
     * @param MicroPostRepository $microPostRepository
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     */
    public function __construct(MicroPostRepository $microPostRepository, FormFactoryInterface $formFactory,
                                EntityManagerInterface $entityManager, RouterInterface $router)
    {
    $this->microPostRepository = $microPostRepository;
    $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index()
    {
        $html = $this->render('micro-post/index.html.twig',
            [
                'posts' => $this->microPostRepository->findAll()
            ]
        );
        return new Response($html);
    }

    /**
     * @Route("/add", name="micro_post_add")
     */
    public function add(Request $request)
    {
        $microPost = new MicroPost();
        $microPost->setTime(new DateTime());

//        var_dump($microPost);
//        die;

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