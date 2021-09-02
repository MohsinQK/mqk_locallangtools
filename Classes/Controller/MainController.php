<?php
namespace Mohsin\MqkLocallangtools\Controller;


/***
 *
 * This file is part of the "MqkLocallangtools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2021 Mohsin Khan <mohsinqayyumkhan@gmail.com>, 3AM
 *
 ***/
/**
 * MainController
 */
class MainController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * index action
     *
     * @return void
     */
    public function indexAction()
    {
        $options = glob('../typo3conf/ext/*', GLOB_ONLYDIR);
        $optionsForTemplate = [];
        foreach ($options as $key => $option) {
            $optionsForTemplate[$option] = basename($option);
        }
        $this->view->assign('options', $optionsForTemplate);
    }

    /**
     * index action
     *
     * @return void
     */
    public function replaceAction()
    {
        $replace = isset($_POST['replace']) ? $_POST['replace'] : [];
        $extensionName = isset($_POST['extensionName']) ? $_POST['extensionName'] : '';
        $locallangKeys = [];
        foreach ($replace as $filePath => $wordsInLine) {
            $fileContent = explode("\n", file_get_contents($filePath));
            foreach ($wordsInLine as $lineNumber => $words) {
                foreach ($words as $word => $on) {
                    $fileContent[$lineNumber] = str_replace($word, $this->fluidTranslate($word), $fileContent[$lineNumber]);
                    $locallangKeys[$this->getLocallangKey($word)] = $word;
                }
            }
            // write $fileContnet back to file
            file_put_contents($filePath, implode("\n", $fileContent));
        }

        $this->addInLocallang($locallangKeys, $extensionName);
    }

    /**
     * index action
     *
     * @return void
     */
    public function listAction()
    {
        if($this->request->hasArgument('extensionName')) {
            $files = $this->rglob($this->request->getArgument('extensionName') . '/*.html');
            $data = [];
            foreach($files as $key => $filePath) {
                $data[$filePath] = [];
                $contents = file($filePath);
                foreach($contents as $lineNumber => $lineContent) {
                    $matches = [];
                    if(preg_match_all('/>([\w :\.\?\&\'\%\/\’]+)</', $lineContent, $matches)) {
                        if($matches[1][0] != ' ' && !is_numeric($matches[1][0])) {
                            $data[$filePath][$lineNumber] = $matches[1];
                        }
                    }
                }
                if(empty($data[$filePath])) {
                    unset($data[$filePath]);
                }
            }
            $this->view->assign('data', $data);
            $this->view->assign('extensionName', $this->request->getArgument('extensionName'));
        }
    }

    /**
     * @param mixed $pattern
     * @param int $flags
     *
     * @return array
     */
    public function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

    /**
     * @param mixed $word
     *
     * @return array
     */
    public function fluidTranslate($word) {
        return '<f:translate key="'.$this->getLocallangKey($word).'" />';
    }

    /**
     * @param mixed $word
     *
     * @return array
     */
    public function getLocallangKey($word) {
        $word = strtolower($word);
        $word = str_replace(' ', '_', $word);
        $word = str_replace(':', '', $word);
        $word = str_replace('%', '', $word);
        $word = str_replace('&', '', $word);
        $word = str_replace('?', '', $word);
        $word = str_replace('.', '', $word);
        $word = str_replace('’', '', $word);
        $word = str_replace("'", '', $word);
        $word = str_replace('/', '', $word);

        return substr($word, 0, 70);
    }

    /**
     * add keys to locallang file of the extension
     *
     * @param mixed $locallangKeys
     * @param mixed $extensionName
     *
     * @return [type]
     */
    public function addInLocallang($locallangKeys, $extensionName) {
        $filePath = $extensionName . '/Resources/Private/Language/locallang.xlf';
        $locallangXml = simplexml_load_file($filePath);
        foreach($locallangKeys as $key => $word) {
            if(!$this->isKeyAlreadyExist($key, $locallangXml)) {
                $transUnit = $locallangXml->file->body->addChild('trans-unit');
                $transUnit->addAttribute('id', $key);
                $transUnit->addAttribute('resname', $key);
                $transUnit->addChild('source', $word);
            }
        }

        $dom = new \DOMDocument();
        // Initial block (must before load xml string)
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        // End initial block

        $dom->loadXML($locallangXml->asXML());
        file_put_contents($filePath, $dom->saveXML());
        $this->redirect('index');
    }

    /**
     * add keys to locallang file of the extension
     *
     * @param mixed $locallangKeys
     * @param mixed $extensionName
     *
     * @return [type]
     */
    public function isKeyAlreadyExist($key, $locallangXml) {

        foreach($locallangXml->file->body->{'trans-unit'} as $unit) {
            if($unit->attributes()->id == $key) {
                return true;
            }
        }

        return false;
    }
}
