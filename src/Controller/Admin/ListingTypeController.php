<?php

namespace App\Controller\Admin;

use App\Entity\ListingType;
use App\Form\ListingTypeFormType;
use App\FormFilter\ListingTypeFilterType;
use App\Repository\ListingTypeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/listing-type")
 */
class ListingTypeController extends Controller
{
    /**
     * @Route("/", name="listingType_index", methods="GET|POST")
     */
    public function indexAction(ListingTypeRepository $listingTypeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(ListingTypeFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('listingTypeFilter');

            return $this->redirect($this->generateUrl('listingType_index'));
        }

        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isValid()) {
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                }
                if ($filterForm->has('type')) {
                    $filterData['type'] = $filterForm->get('type')->getData();
                }

                $session->set('listingTypeFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('listingTypeFilter')) {
            $filterData = $session->get('listingTypeFilter');
            $filterForm = $this->createForm(ListingTypeFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('name', $filterData)) {
                $filterForm->get('name')->setData($filterData['name']);
            }
            if (array_key_exists('type', $filterData)) {
                $filterForm->get('type')->setData($filterData['type']);
            }
        }
        $per_page = $this->getParameter('app.per_page_global');
        $query = $listingTypeRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* page number */,
            $request->query->getInt('per_page', $per_page), /* limit per page */
            ['defaultSortFieldName' => 'c.name', 'defaultSortDirection' => 'asc']
        );

        return $this->render('admin/listingType/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }

    /**
     * @Route("/new", name="listingType_new", methods="GET|POST")
     */
    public function newAction(Request $request): Response
    {
        $listingType = new ListingType();
        $form = $this->createForm(ListingTypeFormType::class, $listingType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($listingType);
            $em->flush();

            $this->addFlash('success', 'Generics.flash.addSuccess');
            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('listingType_index');
            } else {
                $listingType = new ListingType();
                $form = $this->createForm(ListingTypeFormType::class, $listingType);
            }
        }

        return $this->render('admin/listingType/new.html.twig', [
          'entity' => $listingType,
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="listingType_edit", methods="GET|POST")
     */
    public function editAction(Request $request, ListingType $listingType): Response
    {
        $form = $this->createForm(ListingTypeFormType::class, $listingType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Generics.flash.editSuccess');

            return $this->redirectToRoute('listingType_index');
        }

        return $this->render('admin/listingType/edit.html.twig', [
          'entity' => $listingType,
          'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a listingType entity.
     *
     * @param int $id
     *
     * @Route("/{id}/delete", name="listingType_delete",  methods="GET")
     *
     * @return Response
     */
    public function deleteAction($id, ListingTypeRepository $listingTypeRepository)
    {
        $ids = [$id];
        $listingTypeRepository->batchDelete($ids);
        $this->addFlash('success', 'Suppression effectuée avec succès');

        return $this->redirectToRoute('listingType_index');
    }

    /**
     * Batch action for BusinessState entity.
     *
     * @Route("/batch", name="listingType_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, ListingTypeRepository $listingTypeRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                $listingTypeRepository->batchDelete($ids);
                $this->addFlash('success', 'les éléments selectionnés ont été supprimés');
            }
        }

        return $this->redirectToRoute('admin/listingType_index');
    }
}
