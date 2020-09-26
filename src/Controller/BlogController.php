<?php


namespace App\Controller;

use App\Service\Greeting;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BlogController
 * @Route ("/blog")
 */
class BlogController
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(\Twig\Environment $twig, SessionInterface $session)
    {
        $this->twig = $twig;
        $this->session = $session;
    }

    /**
     * First method for blog controller testing
     *
     * @Route("/{name}", name="blog_index")
     */
    public function index($name)
    {
        $html = $this->twig->render('blog/index.html.twig',
        [
            'posts' => $this->session->get('posts')
        ]
        );

        return new Response($html);
    }

    /**
     * @Route("/add", name="blog_add")
     * @throws \Exception
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[uniqid()] = [
            'title' => 'A random title ' . random_int(1, 500),
            'text' => 'Some random text nr. ' . random_int(1, 500),
        ];
        $this->session->set('posts', $posts);
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

        $html = $this->twig->render(
            'blog/post.html.twig',
            [
                'id' =>$id,
                'post' => $posts[$id],
            ]
        );

        return new Response($html);
    }
}