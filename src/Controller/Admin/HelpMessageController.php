<?php

namespace App\Controller\Admin;

use App\Entity\HelpMessage;
use App\Form\HelpMessageFormType as HelpMessageFormType;
use App\FormFilter\HelpMessageFilterType;
use App\Repository\HelpMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin/helpMessage")
 */
class HelpMessageController extends Controller
{
    /**
     * @Route("/", name="helpMessage_index", methods="GET|POST")
     */
    public function indexAction(HelpMessageRepository $helpMessageRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(HelpMessageFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('helpMessageFilter');

            return $this->redirect($this->generateUrl('helpMessage_index'));
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

                $session->set('helpMessageFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('helpMessageFilter')) {
            $filterData = $session->get('helpMessageFilter');
            $filterForm = $this->createForm(HelpMessageFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('name', $filterData)) {
                $filterForm->get('name')->setData($filterData['name']);
            }
            if (array_key_exists('type', $filterData)) {
                $filterForm->get('type')->setData($filterData['type']);
            }
        }
        $per_page = $this->getParameter('app.per_page_global');
        $query = $helpMessageRepository->search($filterData);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/* page number */,
            $request->query->getInt('per_page', $per_page), /* limit per page */
            ['defaultSortFieldName' => 'hm.slug', 'defaultSortDirection' => 'asc']
        );

        return $this->render('admin/helpMessage/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }

    /**
     * @Route("/new", name="helpMessage_new", methods="GET|POST")
     */
    public function newAction(Request $request): Response
    {
        $helpMessage = new HelpMessage();
        $form = $this->createForm(HelpMessageFormType::class, $helpMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($helpMessage);
            $em->flush();

            $this->addFlash('success', 'Generics.flash.addSuccess');
            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('helpMessage_index');
            } else {
                $helpMessage = new HelpMessage();
                $form = $this->createForm(HelpMessageFormType::class, $helpMessage);
            }
        }

        return $this->render('admin/helpMessage/new.html.twig', [
                'entity' => $helpMessage,
                'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="helpMessage_edit", methods="GET|POST")
     */
    public function editAction(Request $request, HelpMessage $helpMessage): Response
    {
        $form = $this->createForm(HelpMessageFormType::class, $helpMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Generics.flash.editSuccess');

            return $this->redirectToRoute('helpMessage_index');
        }

        return $this->render('admin/helpMessage/edit.html.twig', [
                'entity' => $helpMessage,
                'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a listingType entity.
     *
     * @param int $id
     * @Route("/{id}/delete", name="helpMessage_delete",  methods="GET")
     *
     * @return Response
     */
    public function deleteAction($id, HelpMessageRepository $helpMessageRepository)
    {
        $ids = [$id];
        $helpMessageRepository->batchDelete($ids);
        $this->addFlash('success', 'Suppression effectuée avec succès');

        return $this->redirectToRoute('helpMessage_index');
    }

    /**
     * Batch action for BusinessState entity.
     *
     * @param Request $request
     * @Route("/batch", name="helpMessage_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, HelpMessageRepository $helpMessageRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                $helpMessageRepository->batchDelete($ids);
                $this->addFlash('success', 'les éléments selectionnés ont été supprimés');
            }
        }

        return $this->redirectToRoute('helpMessage_index');
    }

    /**
     * Page.
     *
     * @param HelpMessage $helpMessage
     *
     * @return Resopnse
     *
     * @Route("/{id}/showBloc", name="helpMessage_showBloc", methods="GET", options={"expose"=true})
     */
    public function blocShowAction(HelpMessage $helpMessage)
    {
        return $this->render('admin/helpMessage/showBloc.html.twig', [
                'helpMessage' => $helpMessage,
        ]);
    }
}
