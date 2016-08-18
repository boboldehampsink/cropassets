<?php

namespace Craft;

/**
 * Crop Assets Fieldtype.
 *
 * Adds the ability to manually crop and resize assets per field.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2016, Bob Olde Hampsink
 * @license   MIT
 *
 * @link      http://github.com/boboldehampsink
 */
class CropAssetsFieldType extends AssetsFieldType
{
    /**
     * Template to use for field rendering.
     *
     * @var string
     */
    protected $inputTemplate = 'cropAssets/field/_input';

    /**
     * Get fieldtype name.
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Crop Assets');
    }

    /**
     * Return criteria in back-end and cropped images in front-end.
     *
     * @param mixed $value
     *
     * @return CropAssetsModel
     */
    public function prepValue($value)
    {
        // Behave as normal asset in back-end
        if (craft()->request->isCpRequest()) {
            return parent::prepValue($value);
        }

        return $this->prepValueForSite($value);
    }

    /**
     * Override default asset settings - leaving fileKinds out.
     *
     * @return string|null
     */
    public function getSettingsHtml()
    {
        // Create a list of folder options for the main Source setting, and source options for the upload location
        // settings.
        $folderOptions = array();
        $sourceOptions = array();
        $sources = (array) $this->getElementType()->getSources();
        foreach ($sources as $key => $source) {
            if (!isset($source['heading'])) {
                $folderOptions[] = array('label' => $source['label'], 'value' => $key);
            }
        }
        foreach (craft()->assetSources->getAllSources() as $source) {
            $sourceOptions[] = array('label' => $source->name, 'value' => $source->id);
        }
        $namespace = craft()->templates->getNamespace();
        $isMatrix = (strncmp($namespace, 'types[Matrix][blockTypes][', 26) === 0);

        return craft()->templates->render('cropAssets/field/_settings', array(
            'folderOptions' => $folderOptions,
            'sourceOptions' => $sourceOptions,
            'targetLocaleField' => $this->getTargetLocaleFieldHtml(),
            'settings' => $this->getSettings(),
            'type' => $this->getName(),
            'isMatrix' => $isMatrix,
        ));
    }

    /**
     * Update entryId of the cropAsset after saving the element
     *
     * {@inheritdoc}
     */
    public function onAfterElementSave()
    {
        parent::onAfterElementSave();

        $fieldId = $this->model->id;
        $postLocation = preg_replace('/\.([^.]+)$/', '', $this->contentPostLocation);
        $postedFields = craft()->request->getPost($postLocation);

        if (isset($postedFields['cropassets'][$fieldId])) {
            $cropAssetId = $postedFields['cropassets'][$fieldId];
            $cropAsset = craft()->cropAssets->getCropAsset(['id' => $cropAssetId]);
            $cropAsset->entryId = $this->element->id;

            craft()->cropAssets->saveCropAsset($cropAsset);
        }
    }

    // Protected
    // =========================================================================

    /**
     * Let users know we're uploading an image.
     *
     * @return string
     */
    protected function getAddButtonLabel()
    {
        return Craft::t('Add an image');
    }

    /**
     * Returns an array of variables that should be passed to the input template.
     *
     * @param string $name
     * @param mixed  $criteria
     *
     * @return array
     */
    protected function getInputTemplateVariables($name, $criteria)
    {
        $cropAsset = $this->getCropAsset();

        $variables = parent::getInputTemplateVariables($name, $criteria);
        $variables['aspectRatio'] = $this->getSettings()->aspectRatio;
        $variables['cropAsset'] = $cropAsset;

        return $variables;
    }

    /**
     * Limit filekinds to image only.
     *
     * @return array
     */
    protected function defineSettings()
    {
        return array_merge(parent::defineSettings(), array(
            'restrictFiles' => array(AttributeType::Bool, 'default' => true),
            'allowedKinds' => array(AttributeType::Mixed, 'default' => array('image')),
            'limit' => array(AttributeType::Number, 'default' => 1),
            'scaleToFitWidth' => AttributeType::Number,
            'scaleToFitHeight' => AttributeType::Number,
            'aspectRatio' => array(AttributeType::Number, 'decimals' => 2),
        ));
    }

    // Private
    // =========================================================================

    /**
     * Prep value for site.
     *
     * @param mixed $value
     *
     * @return array
     */
    private function prepValueForSite($value)
    {
        $cropAsset = $this->getCropAsset();
        if ($cropAsset->targetAssetId) {
            $value = [$cropAsset->targetAssetId];
        }
        return parent::prepValue($value);
    }

    /**
     * Get crop asset for model and element
     *
     * @return CropAssetModel
     */
    private function getCropAsset()
    {
        return craft()->cropAssets->getCropAsset([
            'entryId' => @$this->element->id,
            'fieldId' => $this->model->id,
        ]);
    }
}
