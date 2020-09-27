<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


class BlogController extends AbstractController
{

    /**
     * @var
     */
    private $session;

    /**
     * @var
     */
    private $router;

    public function __construct(SessionInterface $session, RouterInterface $router)
    {
        $this->session = $session;
    }

    /**
     * First method for blog controller testing
     * @Route("/", name="blog_index")
     */
    public function index()
    {
//        $html = $this->render('blog/index.html.twig',
//            [
//                'posts' => $this->session->get('posts')
//            ]
//        );

        $posts = $this->session->get('posts');

//        return new Response($html);

//        debug($posts);
//        die;

        return $this->render('blog/index.html.twig', array('posts' => $posts));
    }

    /**
     * @Route("/add", name="blog_add")
     * @throws \Exception
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[uniqid('', true)] = [
            'title' => 'A random title ' . random_int(1, 500),
            'text' => 'Some random text nr. ' . random_int(1, 500),
        ];
        $this->session->set('posts', $posts);

        return new RedirectResponse($this->router->generate('blog_index'));
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');

        if (!$posts || !isset($postst[$id])) {
            throw new NotFoundHttpException('Post really not found');
        }

        $html = $this->render(
            'blog/post.html.twig',
            [
                'id' =>$id,
                'post' => $posts[$id],
            ]
        );

        return new Response($html);
    }
}