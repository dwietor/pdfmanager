<?php

namespace DWietor\Bundle\PmanagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Oro\Bundle\LocaleBundle\Model\LocaleSettings;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use DWietor\Bundle\PmanagerBundle\Processor\ProcessorRegistry;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use DWietor\Bundle\PmanagerBundle\Entity\PDFTemplate as PDFTemplateEntity;
use DWietor\Bundle\PmanagerBundle\Entity\Repository\PDFTemplateRepository;
use Oro\Bundle\SecurityBundle\Authentication\Token\UsernamePasswordOrganizationToken;
use Symfony\Component\HttpFoundation\RequestStack;
use Oro\Bundle\FrontendBundle\Request\FrontendHelper;

class ExportPDFType extends AbstractType
{
    const NAME = 'd_wietor_pmanager_exportpdf';

    protected $securityContext;
    protected $requestStack;
    /** @var FrontendHelper */
    protected $frontendHelper;
    /**
     * @param ProcessorRegistry $processorRegistry
     */
    public function __construct(SecurityContextInterface $securityContext,RequestStack $requestStack)
    {
       $this->securityContext = $securityContext;
       $this->requestStack = $requestStack;
    }
    /**
     * @param FrontendHelper $frontendHelper
     */
    public function setFrontendHelper(FrontendHelper $frontendHelper)
    {
        $this->frontendHelper = $frontendHelper;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $styleinline = array();
        if (true == $this->frontendHelper->isFrontendRequest($request)) {
            $styleinline = array('style' => 'width:100%;');
        }
       $builder->add('entityClass', 'hidden', ['required' => true])
       ->add('entityId', 'hidden', ['required' => true]);       
        $builder
            ->add(
                'template',
                'd_wietor_pmanager_pdftemplate_list',
                [
                    'label' => 'oro.email.template.label',
                    'attr' => $styleinline,
                    'required' => true,
                    'depends_on_parent_field' => 'entityClass',
                    'configs' => [
                        'allowClear' => true
                    ]
                ]
            );
            $builder->add(
                'process',
                'choice',
                [
                    'label'      => 'oro.email.type.label',
                    'required'   => true,
                    'data'       => 'download',
                    'choices'  => [
                        'download' => 'dwietor.pmanager.pdftemplate.exportpdf.download_type',
                        'attach'  => 'dwietor.pmanager.pdftemplate.exportpdf.attach_type'
                    ],
                    'expanded'   => true
                ]
            );


        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'initChoicesByEntityName']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'initChoicesByEntityName']);


    }
/**
     * @param FormEvent $event
     */
    public function initChoicesByEntityName(FormEvent $event)
    {
        
        $valuefrompost = $this->requestStack->getCurrentRequest()->get('d_wietor_pmanager_exportpdf');
        if($valuefrompost and isset($valuefrompost['entityClass'])){
        $entityClass = $valuefrompost['entityClass'];
        }
        else{
        $entityClass = $this->requestStack->getCurrentRequest()->get('entityClass');
        $entityClass = trim(str_replace("_","\\",$entityClass));
        }
        //$data = $event->getData();
        if (null === $entityClass){
            return;
        }

        //$entityClass = is_object($data) ? $data->getEntityClass() : $data['entityClass'];
        $form = $event->getForm();
        /** @var UsernamePasswordOrganizationToken $token */
        $token        = $this->securityContext->getToken();
        $organization = $token->getOrganizationContext();

        FormUtils::replaceField(
            $form,
            'template',
            [
                'selectedEntity' => $entityClass,
                'query_builder'  =>
                    function (PDFTemplateRepository $templateRepository) use (
                        $entityClass,
                        $organization
                    ) {
                        return $templateRepository->getEntityTemplatesQueryBuilder($entityClass, $organization, true);
                    },
            ],
            ['choice_list', 'choices']
        );

     /*   if ($this->securityContext->isGranted('EDIT', 'entity:Oro\Bundle\EmailBundle\Entity\EmailUser')) {
            FormUtils::replaceField(
                $form,
                'contexts',
                [
                    'read_only' => false,
                ]
            );
        }*/
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
