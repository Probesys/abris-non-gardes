<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Form\PageFormType as PageFormType;
use App\FormFilter\PageFilterType;
use App\Repository\PageRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin/pages")
 */
class PageController extends Controller
{
    /**
     * @Route("/", name="page_index", methods="GET|POST")
     */
    public function indexAction(PageRepository $pageRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(PageFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('pageFilter');

            return $this->redirect($this->generateUrl('page_index'));
        }


        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                }

                $session->set('pageFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('pageFilter')) {
            $filterData = $session->get('pageFilter');
            $filterForm = $this->createForm(PageFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('name', $filterData)) {
                $filterForm->get('name')->setData($filterData['name']);
            }

        }

        $per_page = $this->getParameter('app.per_page_global');
        $query = $pageRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* page number */,
            $request->query->getInt('per_page', $per_page), /* limit per page */
            ['defaultSortFieldName' => 'p.slug', 'defaultSortDirection' => 'asc']
        );

        return $this->render('admin/page/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }

    /**
     * @Route("/new", name="page_new", methods="GET|POST")
     */
    public function newAction(Request $request): Response
    {
        $page = new Page();

        $form = $this->createForm(PageFormType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            $this->addFlash('success', 'Generics.flash.addSuccess');
            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('page_index');
            } else {
                $page = new Page();
                $form = $this->createForm(PageFormType::class, $page);
            }
        }

        return $this->render('admin/page/new.html.twig', [
                'page' => $page,
                'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="page_edit", methods="GET|POST")
     */
    public function editAction(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageFormType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Generics.flash.editSuccess');

            return $this->redirectToRoute('page_index');
        }

        return $this->render('admin/page/edit.html.twig', [
                'object' => $page,
                'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a page entity.
     *
     * @param int $id
     * @Route("/{id}/delete", name="page_delete",  methods="GET")
     *
     * @return Response
     */
    public function deleteAction($id, PageRepository $pageRepository)
    {
        $ids = [$id];
        $pageRepository->batchDelete($ids);
        $this->addFlash('success', 'Suppression effectuée avec succès');

        return $this->redirectToRoute('page_index');
    }

    /**
     * Batch action for BusinessState entity.
     *
     * @param Request $request
     * @Route("/batch", name="page_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, PageRepository $pageRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                $pageRepository->batchDelete($ids);
                $this->addFlash('success', 'Les éléments selectionnés ont été supprimés');
            }
        }

        return $this->redirectToRoute('admin/page_index');
    }
}
