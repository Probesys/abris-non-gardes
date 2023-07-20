<?php

namespace App\Controller\Admin;

use App\Entity\ListingValue;
use App\Form\ListingValueFormType as ListingValueFormType;
use App\FormFilter\ListingValueFilterType;
use App\Repository\ListingValueRepository;
use App\Repository\ListingTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin/listing-value")
 */
class ListingValueController extends Controller
{
    /**
     * @Route("/", name="listingValue_index", methods="GET|POST")
     */
    public function indexAction(ListingValueRepository $listingValueRepository, ListingTypeRepository $listingTypeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(ListingValueFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('listingValueFilter');

            return $this->redirect($this->generateUrl('listingValue_index'));
        }

        if ($request->query->has('listingTypeId')) {
            $listingType = $listingTypeRepository->find($request->query->get('listingTypeId'));
            $filterData['listingType'] = $listingType;
            $session->set('listingValueFilter', $filterData);
        }
        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                }
                if ($filterForm->has('listingType')) {
                    $filterData['listingType'] = $filterForm->get('listingType')->getData();
                }
                if ($filterForm->has('parent')) {
                    $filterData['parent'] = $filterForm->get('parent')->getData();
                }


                $session->set('listingValueFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('listingValueFilter')) {
            $filterData = $session->get('listingValueFilter');

            $filterForm = $this->createForm(ListingValueFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('name', $filterData)) {
                $filterForm->get('name')->setData($filterData['name']);
            }
            if (array_key_exists('listingType', $filterData)) {
                $filterForm->get('listingType')->setData($filterData['listingType']);
            }
            if (array_key_exists('parent', $filterData)) {
                $filterForm->get('parent')->setData($filterData['parent']);
            }
        }

        $per_page = $this->getParameter('app.per_page_global');
        $query = $listingValueRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* page number */,
            $request->query->getInt('per_page', $per_page), /* limit per page */
            ['defaultSortFieldName' => 'lt.slug', 'defaultSortDirection' => 'asc']
        );
        return $this->render('admin/listingValue/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }

    /**
     * @Route("/new", name="listingValue_new", methods="GET|POST")
     */
    public function newAction(Request $request, ListingTypeRepository $listingTypeRepository, TranslatorInterface $translator): Response
    {
        $listingValue = new ListingValue();

        if ($request->query->has('listingTypeId')) {
            $listingType = $listingTypeRepository->find($request->query->get('listingTypeId'));
            $listingValue->setListingType($listingType);
        }

        $form = $this->createForm(ListingValueFormType::class, $listingValue, ['translator' => $translator]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($listingValue);
            $em->flush();

            $this->addFlash('success', 'Generics.flash.addSuccess');
            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('listingValue_edit', ['id' => $listingValue->getId()]);
            } else {
                $listingType = $listingValue->getListingType();
                $listingValue = new ListingValue();
                $listingValue->setListingType($listingType);
                $form = $this->createForm(ListingValueFormType::class, $listingValue, ['translator' => $translator]);
            }
        }

        return $this->render('admin/listingValue/new.html.twig', [
                    'listingValue' => $listingValue,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="listingValue_edit", methods="GET|POST")
     */
    public function editAction(Request $request, ListingValue $listingValue, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ListingValueFormType::class, $listingValue, ['translator' => $translator]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Generics.flash.editSuccess');

            return $this->redirectToRoute('listingValue_edit', ['id' => $listingValue->getId()]);
        }

        return $this->render('admin/listingValue/edit.html.twig', [
                    'listingValue' => $listingValue,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a listingValue entity.
     *
     * @param int $id
     * @Route("/{id}/delete", name="listingValue_delete",  methods="GET")
     *
     * @return Response
     */
    public function deleteAction($id, ListingValueRepository $listingValueRepository)
    {
        $ids = [$id];
        if ($listingValueRepository->batchDelete($ids)) {
            $this->addFlash('success', 'Suppression effectuée avec succès');
        } else {
            $this->addFlash('error', 'Une erreur s\'est produite lors de la suppression. Cette valeur est probalement utilisée');
        }
        ;

        return $this->redirectToRoute('listingValue_index');
    }

    /**
     * Batch action for BusinessState entity.
     *
     * @param Request $request
     * @Route("/batch", name="listingValue_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, ListingValueRepository $listingValueRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                if ($listingValueRepository->batchDelete($ids)) {
                    $this->addFlash('success', 'Les éléments selectionnés ont été supprimés');
                } else {
                    $this->addFlash('error', 'Une erreur s\'est produite lors de la suppression. Une valeur est probalement utilisée');
                }
            }
        }

        return $this->redirectToRoute('listingValue_index');
    }

}
