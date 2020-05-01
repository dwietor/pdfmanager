<?php

namespace DWietor\Bundle\PmanagerBundle\Provider;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\LayoutBundle\Provider\CustomImageFilterProviderInterface;
use Oro\Bundle\ProductBundle\DependencyInjection\Configuration;
class ConfigurationProvider
{
    const Allow_FIELD = 'd_wietor_pmanager.allow';
    const Logo_FIELD = 'd_wietor_pmanager.logo';
    const LogoSize_FIELD = 'd_wietor_pmanager.logosize';   
    const TextHeader_FIELD = 'd_wietor_pmanager.textheader';
    const TitleHeader_FIELD = 'd_wietor_pmanager.titleheader';
    const MarginHeader_FIELD = 'd_wietor_pmanager.marginheader';
    const MarginFooter_FIELD = 'd_wietor_pmanager.marginfooter';
    
    const EnableMultiTemplate_FIELD = 'd_wietor_pmanager.enablemultitemplate';
    const DefaultOrderTemplate_FIELD = 'd_wietor_pmanager.defaultordertemplate';
    const DefaultQuoteTemplate_FIELD = 'd_wietor_pmanager.defaultquotetemplate';

    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager, DoctrineHelper $doctrineHelper, $attachmentDir)
    {
        $this->configManager = $configManager;
        $this->doctrineHelper = $doctrineHelper;
        $this->attachmentDir = $attachmentDir;
    }

    /**
     * @return string
     */
    public function getAllowed()
    {
        return $this->configManager->get(self::Allow_FIELD);
    }
    /**
     * @return string
     */
    public function getLogo()
    {       
        $imageId = $this->configManager->get(self::Logo_FIELD);
        $filePath = "";
        if ($imageId && $image = $this->doctrineHelper->getEntityRepositoryForClass(File::class)->find($imageId)) {
            /** @var File $image */
            $filePath = $this->attachmentDir . '/' . $image->getFilename();
        }
        return $filePath;       
    }
    /**
     * @return string
     */
    public function getLogoSize()
    {
        return $this->configManager->get(self::LogoSize_FIELD);
    }
    /**
     * @return string
     */
    public function getTextHeader()
    {
        return $this->configManager->get(self::TextHeader_FIELD);
    }
    /**
     * @return string
     */
    public function getTitleHeader()
    {
        return $this->configManager->get(self::TitleHeader_FIELD);
    }
    /**
     * @return string
     */
    public function getMarginHeader()
    {
        return $this->configManager->get(self::MarginHeader_FIELD);
    }
    /**
     * @return string
     */
    public function getMarginFooter()
    {
        return $this->configManager->get(self::MarginFooter_FIELD);
    }
    /**
     * @return string
     */
    public function getDefaultQuoteTemplate()
    {
        return $this->configManager->get(self::DefaultQuoteTemplate_FIELD);
    }
    /**
     * @return string
     */
    public function getDefaultOrderTemplate()
    {
        return $this->configManager->get(self::DefaultOrderTemplate_FIELD);
    }
    /**
     * @return string
     */
    public function getEnableMultiTemplate()
    {
        return $this->configManager->get(self::EnableMultiTemplate_FIELD);
    }
}
