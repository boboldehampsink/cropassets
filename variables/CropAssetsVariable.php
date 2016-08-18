<?php
namespace Craft;

/**
 * Crop Assets Variable.
 *
 * Adds the ability to manually crop and resize assets per field.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2016, Bob Olde Hampsink
 * @license   MIT
 *
 * @link      http://github.com/boboldehampsink
 */
class CropAssetsVariable
{
    /**
     * Get asset source options for settings field
     *
     * @return array
     */
    public function getAssetSourceOptions()
    {
        $assetSources = craft()->assetSources->getAllSources();
        return array_map(array($this, 'getAssetSourceOption'), $assetSources);
    }

    /**
     * @param AssetSourceModel $source
     * @return array
     */
    private function getAssetSourceOption(AssetSourceModel $source)
    {
        return array(
            'label' => $source->name,
            'value' => $source->handle,
        );
    }
}
