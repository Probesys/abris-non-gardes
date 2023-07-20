<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Form\CityFormType as CityFormType;
use App\FormFilter\CityFilterType;
use App\Repository\CityRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin/cities")
 */
class CityController extends Controller
{
    /**
     * @Route("/", name="city_index", methods="GET|POST")
     */
    public function indexAction(CityRepository $cityRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(CityFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('cityFilter');

            return $this->redirect($this->generateUrl('city_index'));
        }


        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                }
                if ($filterForm->has('insee')) {
                    $filterData['insee'] = $filterForm->get('insee')->getData();
                }
                if ($filterForm->has('zipCode')) {
                    $filterData['zipCode'] = $filterForm->get('zipCode')->getData();
                }
                if ($filterForm->has('department')) {
                    $filterData['department'] = $filterForm->get('department')->getData();
                }
                if ($filterForm->has('isActive')) {
                    $filterData['isActive'] = $filterForm->get('isActive')->getData();
                }
                if ($filterForm->has('territories')) {
                    $filterData['territories'] = $filterForm->get('territories')->getData();
                }
                if ($filterForm->has('id')) {
                    $filterData['id'] = $filterForm->get('id')->getData();
                }

                $session->set('cityFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('cityFilter')) {
            $filterData = $session->get('cityFilter');
            $filterForm = $this->createForm(CityFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('name', $filterData)) {
                $filterForm->get('name')->setData($filterData['name']);
            }

        }

        $per_page = $this->getParameter('app.per_page_global');
        $query = $cityRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* city number */,
            $request->query->getInt('per_page', $per_page), /* limit per city */
            ['defaultSortFieldName' => 'c.slug', 'defaultSortDirection' => 'asc']
        );

        return $this->render('admin/city/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }

    /**
     * @Route("/new", name="city_new", methods="GET|POST")
     */
    public function newAction(Request $request): Response
    {
        $city = new City();

        $form = $this->createForm(CityFormType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();

            $this->addFlash('success', 'Generics.flash.addSuccess');
            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('city_index');
            } else {
                $city = new City();
                $form = $this->createForm(CityFormType::class, $city);
            }
        }

        return $this->render('admin/city/new.html.twig', [
                'city' => $city,
                'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="city_edit", methods="GET|POST")
     */
    public function editAction(Request $request, City $city): Response
    {
        $form = $this->createForm(CityFormType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Generics.flash.editSuccess');

            return $this->redirectToRoute('city_index');
        }

        return $this->render('admin/city/edit.html.twig', [
                'object' => $city,
                'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a city entity.
     *
     * @param int $id
     * @Route("/{id}/delete", name="city_delete",  methods="GET")
     *
     * @return Response
     */
    public function deleteAction($id, CityRepository $cityRepository)
    {
        $ids = [$id];
        $cityRepository->batchDelete($ids);
        $this->addFlash('success', 'Suppression effectuée avec succès');

        return $this->redirectToRoute('city_index');
    }

    /**
     * Batch action for BusinessState entity.
     *
     * @param Request $request
     * @Route("/batch", name="city_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, CityRepository $cityRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                $cityRepository->batchDelete($ids);
                $this->addFlash('success', 'Les éléments selectionnés ont été supprimés');
            }
        }

        return $this->redirectToRoute('city_index');
    }
}
