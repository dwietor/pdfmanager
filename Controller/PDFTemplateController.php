<?php

namespace DWietor\Bundle\PmanagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DWietor\Bundle\PmanagerBundle\Entity\PDFTemplate;

class PDFTemplateController extends Controller
{
     /**
     * @Route("/pmanager/template/index", name="pmanager_template_index")
     * @Template()
     * @Acl(
     *      id="pmanager_template_index",
     *      type="entity",
     *      class="DWietorPmanagerBundle:PDFTemplate",
     *      permission="VIEW"
     * )
     */
    public function indexAction()
    {
       return array();
    }
    /**
     * @Route("pmanager/template/update/{id}", name="pmanager_template_update", requirements={"id"="\d+"}, defaults={"id"=0}))
     * @Acl(
     *      id="pmanager_template_update",
     *      type="entity",
     *      class="DWietorPmanagerBundle:PDFTemplate",
     *      permission="EDIT"
     * )
     * @Template()
     */
    public function updateAction(PDFTemplate $entity, $isClone = false)
    {
        return $this->update($entity, $isClone);
    }

    /**
     * @Route("pmanager/template/create", name="pmanager_template_create")
     * @Acl(
     *      id="pmanager_template_create",
     *      type="entity",
     *      class="DWietorPmanagerBundle:PDFTemplate",
     *      permission="CREATE"
     * )
     * @Template("DWietorPmanagerBundle:PDFTemplate:update.html.twig")
     */
    public function createAction()
    {
        return $this->update(new PDFTemplate());
    }

    /**
     * @Route("pmanager/template/clone/{id}" , name="pmanager_template_clone" , requirements={"id"="\d+"}, defaults={"id"=0}))
     * @AclAncestor("pmanager_template_create")
     * @Template("DWietorPmanagerBundle:PDFTemplate:update.html.twig")
     */
    public function cloneAction(PDFTemplate $entity)
    {
        return $this->update(clone $entity, true);
    }

    /**
     * @Route("pmanager/template/preview/{id}" , name="pmanager_template_preview" , requirements={"id"="\d+"}, defaults={"id"=0}))
     * @Acl(
     *      id="pmanager_template_preview",
     *      type="entity",
     *      class="DWietorPmanagerBundle:PDFTemplate",
     *      permission="VIEW"
     * )
     * @Template("DWietorPmanagerBundle:PDFTemplate:preview.html.twig")
     * @param bool|int $id
     * @return array
     */
    public function previewAction($id = false)
    {
        if (!$id) {
            $pdfTemplate = new PDFTemplate();
        } else {
            /** @var EntityManager $em */
            $em = $this->get('doctrine.orm.entity_manager');
            $pdfTemplate = $em->getRepository('DWietor\Bundle\PmanagerBundle\Entity\PDFTemplate')->find($id);
        }

        /** @var FormInterface $form */
        $form = $this->get('d_wietor_pmanager.form.pdftemplate');
        $form->setData($pdfTemplate);
        $request = $this->get('request');

        if (in_array($request->getMethod(), array('POST', 'PUT'))) {
            $form->submit($request);
        }

        $templateRendered = $this->get('d_wietor_pmanager.pdftemplate_renderer')
            ->compilePreview($pdfTemplate);

        return array(
            'content'     => $templateRendered,
            'contentType' => $pdfTemplate->getType()
        );
    }

    /**
     * @param EmailTemplate $entity
     * @param bool $isClone
     * @return array
     */
    protected function update(PDFTemplate $entity, $isClone = false)
    {
        if ($this->get('d_wietor_pmanager.form.handler.pdftemplate')->process($entity)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('dwietor.pmanager.pdftemplate.saved.message')
            );

            return $this->get('oro_ui.router')->redirectAfterSave(
                ['route' => 'pmanager_template_update', 'parameters' => ['id' => $entity->getId()]],
                ['route' => 'pmanager_template_index'],
                $entity
            );
        }

        return array(
            'entity'  => $entity,
            'form'    => $this->get('d_wietor_pmanager.form.pdftemplate')->createView(),
            'isClone' => $isClone
        );
    }
}
