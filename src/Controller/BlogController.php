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


/**
 * Class BlogController
 * @package App\Controller

 */
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
     * First method for blog controller testing - basic blog index
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        $html = $this->render('blog/index.html.twig',
            [
                'posts' => $this->session->get('posts')
            ]
        );
        return new Response($html);
    }

    /**
     * Add (randomly generated) blog post
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[uniqid('', true)] = [
            'title' => 'A random titleeee ' . random_int(1, 500),
            'text' => 'Some random text nr. ' . random_int(1, 500),
            'date' => new \DateTime(),
        ];
        $this->session->set('posts', $posts);

        return $this->redirectToRoute('blog_index');
    }

    /**
     * Basic method for showing blog detail
     * @Route("/show/{id}", name="blog_show")
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');

        if (!$posts || !isset($posts[$id])) {
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