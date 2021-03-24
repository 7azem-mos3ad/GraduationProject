<?php
class NI_files
{
    public static function upload()
    {
        $args =func_get_args();
        $input = $args[0];
        $upload = new \Delight\FileUpload\FileUpload();
        $upload->withTargetDirectory(STORAGE);
        $upload->from($input);
        if (isset($args[1]) && is_integer($args[1])) {
            $upload->withMaximumSizeInMegabytes($args[1]);
        }
        if (isset($args[2]) && is_array($args[2])) {
            $upload->withAllowedExtensions($args[2]);
        }
        try {
            $uploadedFile = $upload->save();
        
            return $uploadedFile->getFilenameWithExtension();
        } catch (\Delight\FileUpload\Throwable\InputNotFoundException $e) {
            return 'input not found';
        } catch (\Delight\FileUpload\Throwable\InvalidFilenameException $e) {
            echo ' invalid filename';
        } catch (\Delight\FileUpload\Throwable\InvalidExtensionException $e) {
            echo ' invalid extension';
        } catch (\Delight\FileUpload\Throwable\FileTooLargeException $e) {
            echo ' file too large';
        } catch (\Delight\FileUpload\Throwable\UploadCancelledException $e) {
            echo ' upload cancelled';
        }
    }
}
