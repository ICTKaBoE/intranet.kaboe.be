<?php

use DocxMerge\DocxMerge;

class DocxMergeTest extends PHPUnit_Framework_TestCase
{
    public function test_setValues()
    {
        $doc = new DocxMerge();
        $doc->setValues(__DIR__ . '/test_src/test_doc.docx', __DIR__ . '/test_src/result/test_set_value.docx', ['name' => 'test_name']);
    }

    public function test_merge()
    {
        $dm = new DocxMerge();
        $dm->merge([
            __DIR__ . '/test_src/test_doc.docx',
            __DIR__ . '/test_src/test_doc2.docx',
        ],
            __DIR__ . '/test_src/result/test_doc_merge.docx'
        );
    }

    public function test_merge_with_page_break()
    {
        $dm = new DocxMerge();
        $dm->merge([
            __DIR__ . '/test_src/test_doc.docx',
            __DIR__ . '/test_src/test_doc2.docx',
        ],
            __DIR__ . '/test_src/result/test_doc_merge_page_breakdocx',
            true
        );
    }
}
