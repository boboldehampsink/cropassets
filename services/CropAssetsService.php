<?php

namespace Craft;

/**
 * Crop Assets Service.
 *
 * Adds the ability to manually crop and resize assets per field.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2016, Bob Olde Hampsink
 * @license   MIT
 *
 * @link      http://github.com/boboldehampsink
 */
class CropAssetsService extends BaseApplicationComponent
{
    /**
     * Get the asset source to store the cropped image in
     *
     * @return AssetSourceModel
     */
    public function getAssetSource()
    {
        $sourceTypeHandle = craft()->plugins->getPlugin('cropAssets')->getSettings()->assetSource;

        $record = AssetSourceRecord::model()->findByAttributes(['handle' => $sourceTypeHandle]);
        return $record ? AssetSourceModel::populateModel($record) : null;
    }

    /**
     * Get the size of the modal based on the asset
     *
     * @param  AssetFileModel $file
     * @return array
     */
    public function getCropModalSize(AssetFileModel $asset)
    {
        $aspectRatio = $asset->getWidth() / $asset->getHeight();
        if ($aspectRatio > 1) {
            $width = 500;
            $height = 500 / $aspectRatio;
        } else {
            $height = 500;
            $width = 500 * $aspectRatio;
        }
        return [$width, $height];
    }

    /**
     * Get a CropAssetsmodel by sourceAssetId, or new if it does not exist
     *
     * @param  int $sourceAssetId
     * @return CropAssetsModel
     */
    public function getCropAssetsModelBySource($sourceAssetId)
    {
        return $this->getCropAsset(['sourceAssetId' => $sourceAssetId]);
    }

    /**
     * Get crop assets by attributes
     *
     * @param  array  $attributes
     * @return CropAssetsModel
     */
    public function getCropAsset(array $attributes)
    {
        $model = new CropAssetsModel();
        $record = CropAssetsRecord::model()->findByAttributes($attributes);
        if ($record) {
            $model = CropAssetsModel::populateModel($record);
        }

        return $model;
    }

    /**
     * Save a crop assets record
     *
     * @param  CropAssetsModel $cropAssets
     * @return bool
     */
    public function saveCropAsset(CropAssetsModel $cropAsset)
    {
        $record = CropAssetsRecord::model()->findByAttributes(['id' => $cropAsset->id]);
        if ($record === null) {
            $record = new CropAssetsRecord();
        }

        $record->setAttributes($cropAsset->getAttributes());
        $record->settings = $cropAsset->settings;

        $success = $record->save();
        $cropAsset->setAttributes($record->getAttributes());
        return $success;
    }

    /**
     * Upload a cropped asset
     *
     * @return AssetOperationResponseModel
     */
    public function uploadCroppedAsset(AssetSourceModel $source)
    {
        $uploadedFile = UploadedFile::getInstanceByName('croppedImage');

        if ($uploadedFile) {
            $folder = craft()->assets->getRootFolderBySourceId($source->id);
            $assetOperationResult = craft()->assets->insertFileByLocalPath($uploadedFile->tempName, $uploadedFile->name, $folder->id, AssetConflictResolution::KeepBoth);
        } else {
            $assetOperationResult = new AssetOperationResponseModel();
            $assetOperationResult->setsetError('No source image was found');
        }

        return $assetOperationResult;
    }
}
