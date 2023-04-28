<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Form\MessageFormType as MessageFormType;
use App\FormFilter\MessageFilterType;
use App\Repository\MessageRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin/messages")
 */
class MessageController extends Controller
{
    /**
     * @Route("/", name="admin_message_index", methods="GET|POST")
     */
    public function indexAction(MessageRepository $messageRepository, Request $request, PaginatorInterface $paginator): Response {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(MessageFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('messageFilter');

            return $this->redirect($this->generateUrl('admin_message_index'));
        }


        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                if ($filterForm->has('abris')) {
                    $filterData['abris'] = $filterForm->get('abris')->getData();
                }
                $session->set('messageFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('messageFilter')) {
            $filterData = $session->get('messageFilter');
            $filterForm = $this->createForm(MessageFilterType::class, $filterData, ['data_class' => null]);
            if (array_key_exists('abris', $filterData)) {
                $filterForm->get('abris')->setData($filterData['abris']);
            }
            
        }
        
        // envoi du user logué si son rôle est autre que admin
        if(!$this->getUser()->hasRole('ROLE_ADMIN')) {
            $filterData['userID'] = $this->getUser()->getId();
        }

        $per_page = $this->getParameter('app.per_page_global');
        $query = $messageRepository->search($filterData);
        $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1)/* page number */,
                $request->query->getInt('per_page', $per_page), /* limit per page */
                ['defaultSortFieldName' => 'm.created', 'defaultSortDirection' => 'desc']
        );

        return $this->render('admin/message/index.html.twig', ['pagination' => $pagination, 'search_form' => $filterForm->createView()]);
    }

    

    /**
     * Delete a message entity.
     *
     * @param int $id
     * @Route("/{id}/delete", name="admin_message_delete",  methods="GET")
     *
     * @return Response
     */
    public function deleteAction($id, MessageRepository $messageRepository)
    {
        $ids = [$id];
        $messageRepository->batchDelete($ids);
        $this->addFlash('success', 'Suppression effectuée avec succès');

        return $this->redirectToRoute('admin_message_index');
    }

    /**
     * Batch action for mesage entity.
     *
     * @param Request $request
     * @Route("/batch", name="admin_message_batch",  methods="POST")
     *
     * @return Response
     */
    public function batchAction(Request $request, MessageRepository $messageRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');
            if ($ids) {
                $messageRepository->batchDelete($ids);
                $this->addFlash('success', 'Les éléments selectionnés ont été supprimés');
            }
        }

        return $this->redirectToRoute('admin_message_index');
    }
}
