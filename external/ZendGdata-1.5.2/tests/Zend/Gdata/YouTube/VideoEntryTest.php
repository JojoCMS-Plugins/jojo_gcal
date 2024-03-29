<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category     Zend
 * @package      Zend_Gdata_YouTube
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com);
 * @license      http://framework.zend.com/license/new-bsd     New BSD License
 * @version      $Id: VideoEntryTest.php 8754 2008-03-11 19:19:45Z rboyd $
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Gdata/YouTube/VideoEntry.php';
require_once 'Zend/Gdata/YouTube/Extension/State.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @category     Zend
 * @package      Zend_Gdata_YouTube
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com);
 * @license      http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_YouTube_VideoEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/Gdata/YouTube/_files/VideoEntryDataSample1.xml',
                true);
        $this->entry = new Zend_Gdata_YouTube_VideoEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($videoEntry) {
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E',
            $videoEntry->id->text);
        $this->assertEquals('UMFI1hdm96E', $videoEntry->getVideoId());
        $this->assertEquals('2007-01-07T01:50:15.000Z', $videoEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $videoEntry->category[0]->scheme);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007#video', $videoEntry->category[0]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[1]->scheme);
        $this->assertEquals('barkley', $videoEntry->category[1]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[2]->scheme);
        $this->assertEquals('singing', $videoEntry->category[2]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[3]->scheme);
        $this->assertEquals('acoustic', $videoEntry->category[3]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[4]->scheme);
        $this->assertEquals('cover', $videoEntry->category[4]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/categories.cat', $videoEntry->category[5]->scheme);
        $this->assertEquals('Music', $videoEntry->category[5]->term);
        $this->assertEquals('Music', $videoEntry->category[5]->label);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[6]->scheme);
        $this->assertEquals('gnarls', $videoEntry->category[6]->term);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/keywords.cat', $videoEntry->category[7]->scheme);
        $this->assertEquals('music', $videoEntry->category[7]->term);

        $this->assertEquals('text', $videoEntry->title->type);
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $videoEntry->title->text);;
        $this->assertEquals('html', $videoEntry->content->type);
        $this->assertEquals('self', $videoEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('self')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E', $videoEntry->getLink('self')->href);
        $this->assertEquals('text/html', $videoEntry->getLink('alternate')->type);
        $this->assertEquals('http://www.youtube.com/watch?v=UMFI1hdm96E', $videoEntry->getLink('alternate')->href);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.responses')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E/responses', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.responses')->href);
        $this->assertEquals('application/atom+xml', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.related')->type);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E/related', $videoEntry->getLink('http://gdata.youtube.com/schemas/2007#video.related')->href);
        $this->assertEquals('davidchoimusic', $videoEntry->author[0]->name->text);
        $this->assertEquals('http://gdata.youtube.com/feeds/users/davidchoimusic', $videoEntry->author[0]->uri->text);
        $mediaGroup = $videoEntry->mediaGroup;

        $this->assertEquals('plain', $mediaGroup->title->type);
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $mediaGroup->title->text);
        $this->assertEquals('plain', $mediaGroup->description->type);
        $this->assertEquals('Gnarles Barkley acoustic cover http://www.myspace.com/davidchoimusic', $mediaGroup->description->text);
        $this->assertEquals('music, singing, gnarls, barkley, acoustic, cover', $mediaGroup->keywords->text);
        $this->assertEquals(255, $mediaGroup->duration->seconds);
        $this->assertEquals('Music', $mediaGroup->category[0]->label);
        $this->assertEquals('http://gdata.youtube.com/schemas/2007/categories.cat', $mediaGroup->category[0]->scheme);
        $this->assertEquals('video', $mediaGroup->content[0]->medium);
        $this->assertEquals('http://www.youtube.com/v/UMFI1hdm96E', $mediaGroup->content[0]->url);
        $this->assertEquals('application/x-shockwave-flash', $mediaGroup->content[0]->type);
        $this->assertEquals('true', $mediaGroup->content[0]->isDefault);
        $this->assertEquals('full', $mediaGroup->content[0]->expression);
        $this->assertEquals(255, $mediaGroup->content[0]->duration);
        $this->assertEquals(5, $mediaGroup->content[0]->format);
        $this->assertEquals('http://www.youtube.com/watch?v=UMFI1hdm96E', $mediaGroup->player[0]->url);

        $this->assertEquals('http://img.youtube.com/vi/UMFI1hdm96E/2.jpg', $mediaGroup->thumbnail[0]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[0]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[0]->width);
        $this->assertEquals('00:02:07.500', $mediaGroup->thumbnail[0]->time);
        $this->assertEquals('http://img.youtube.com/vi/UMFI1hdm96E/1.jpg', $mediaGroup->thumbnail[1]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[1]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[1]->width);
        $this->assertEquals('00:01:03.750', $mediaGroup->thumbnail[1]->time);
        $this->assertEquals('http://img.youtube.com/vi/UMFI1hdm96E/3.jpg', $mediaGroup->thumbnail[2]->url);
        $this->assertEquals(97, $mediaGroup->thumbnail[2]->height);
        $this->assertEquals(130, $mediaGroup->thumbnail[2]->width);
        $this->assertEquals('00:03:11.250', $mediaGroup->thumbnail[2]->time);
        $this->assertEquals('http://img.youtube.com/vi/UMFI1hdm96E/0.jpg', $mediaGroup->thumbnail[3]->url);
        $this->assertEquals(240, $mediaGroup->thumbnail[3]->height);
        $this->assertEquals(320, $mediaGroup->thumbnail[3]->width);
        $this->assertEquals('00:02:07.500', $mediaGroup->thumbnail[3]->time);

        $this->assertEquals(113321, $videoEntry->statistics->viewCount);
        $this->assertEquals(1, $videoEntry->rating->min);
        $this->assertEquals(5, $videoEntry->rating->max);
        $this->assertEquals(1005, $videoEntry->rating->numRaters);
        $this->assertEquals(4.77, $videoEntry->rating->average);
        $this->assertEquals('http://gdata.youtube.com/feeds/videos/UMFI1hdm96E/comments', $videoEntry->comments->feedLink->href);

        $this->assertEquals('37.398529052734375 -122.0635986328125', $videoEntry->where->point->pos->text);
    }

    public function testGetVideoId() {
        $videoEntry = new Zend_Gdata_YouTube_VideoEntry();

        // assert valid ID
        $videoEntry->id = new Zend_Gdata_App_Extension_Id('http://gdata.youtube.com/feeds/videos/ABCDEFG12AB');
        $this->assertEquals('ABCDEFG12AB', $videoEntry->getVideoId());
    }

    /**
     * @expectedException Zend_Gdata_App_Exception
     */
    public function testGetVideoIdException() {
        $videoEntry = new Zend_Gdata_YouTube_VideoEntry();

        // assert invalid ID
        $videoEntry->id = new Zend_Gdata_App_Extension_Id('adfadfasf');
        $this->assertEquals('adfadfasf', $videoEntry->getVideoId());
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testEmptyVideoEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newVideoEntry = new Zend_Gdata_YouTube_VideoEntry();
        $newVideoEntry->transferFromXML($entryXml);
        $newVideoEntryXml = $newVideoEntry->saveXML();
        $this->assertTrue($entryXml == $newVideoEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testGetVideoTitle() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $this->assertEquals('"Crazy (Gnarles Barkley)" - Acoustic Cover', $videoEntry->getVideoTitle());

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getVideoTitle());
    }

    public function testGetVideoDescription() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $this->assertEquals('Gnarles Barkley acoustic cover http://www.myspace.com/davidchoimusic', $videoEntry->getVideoDescription());

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getVideoDescription());
    }

    public function testGetVideoWatchPageUrl() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $this->assertEquals('http://www.youtube.com/watch?v=UMFI1hdm96E', $videoEntry->getVideoWatchPageUrl());

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getVideoWatchPageUrl());
    }

    public function testGetVideoThumbnails() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $mediaThumbnails1 = $videoEntry->getMediaGroup()->getThumbnail();
        $mediaThumbnails2 = $videoEntry->getVideoThumbnails();
        $this->assertEquals(count($mediaThumbnails1), count($mediaThumbnails2));

        $foundThumbnail = false;
        foreach ($mediaThumbnails2 as $thumbnail) {
            if ($thumbnail['url'] == 'http://img.youtube.com/vi/UMFI1hdm96E/1.jpg') {
                $foundThumbnail = true;
                $this->assertEquals(97, $thumbnail['height']);
                $this->assertEquals(130, $thumbnail['width']);
                $this->assertEquals('00:01:03.750', $thumbnail['time']);
            }
        }
        $this->assertTrue($foundThumbnail);

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(array(), $newEntry->getVideoThumbnails());
    }

    public function testGetVideoTags() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;

        $keywords = $videoEntry->getMediaGroup()->getKeywords();

        $keywordsString = (string) $keywords;

        if (strlen(trim($keywordsString)) > 0) {
            $keywordArray = split('(, *)|,', $keywordsString);
        }

        $tagArray = $videoEntry->getVideoTags();
        $this->assertEquals(count($keywordArray), count($tagArray));
        foreach ($keywordArray as $keyword) {
            $this->assertTrue(in_array($keyword, $tagArray));
        }
        foreach ($tagArray as $tag) {
            $this->assertTrue(in_array($tag, $keywordArray));
        }

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(array(), $newEntry->getVideoTags());
    }

    public function testGetFlashPlayerUrl() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $this->assertEquals('http://www.youtube.com/v/UMFI1hdm96E', $videoEntry->getFlashPlayerUrl());

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getFlashPlayerUrl());
    }

    public function testGetVideoDuration() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $this->assertEquals(255, $videoEntry->getVideoDuration());

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getVideoDuration());
    }

    public function testGetVideoViewCount() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $this->assertEquals(113321, $videoEntry->getVideoViewCount());

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getVideoViewCount());
    }

    public function testGetVideoGeoLocation() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $geoLocation =  $videoEntry->getVideoGeoLocation();
        $this->assertEquals('37.398529052734375', $geoLocation['latitude']);
        $this->assertEquals('-122.0635986328125', $geoLocation['longitude']);

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getVideoGeoLocation());
    }

    public function testGetVideoRatingInfo() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;

        $ratingInfo = $videoEntry->getVideoRatingInfo();

        $this->assertEquals(4.77, $ratingInfo['average']);
        $this->assertEquals(1005, $ratingInfo['numRaters']);

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getVideoRatingInfo());
    }

    public function testGetVideoCategory() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;
        $this->assertEquals('Music', $videoEntry->getVideoCategory());

        $newEntry = new Zend_Gdata_YouTube_VideoEntry();
        $this->assertEquals(null, $newEntry->getVideoCategory());
    }

    public function testConvertVideoEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newVideoEntry = new Zend_Gdata_YouTube_VideoEntry();
        $newVideoEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newVideoEntry);
        $newVideoEntryXml = $newVideoEntry->saveXML();
        $this->assertEquals($entryXml, $newVideoEntryXml);
    }

    public function testNoEmbed() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;

        $this->assertNotEquals(null, $videoEntry->getNoEmbed());
    }

    public function testVideoState() {
        $this->entry->transferFromXML($this->entryText);
        $videoEntry = $this->entry;

        $videoState = $videoEntry->getVideoState();
        $this->assertTrue($videoState instanceof Zend_Gdata_YouTube_Extension_State);

        $this->assertEquals('rejected', $videoState->getName());
        $this->assertEquals('inappropriate', $videoState->getReasonCode());
        $this->assertEquals('http://www.youtube.com/t/community_guidelines', $videoState->getHelpUrl());
        $this->assertEquals('The content of this video may violate the terms of use.', 
                            $videoState->getText());
    }

}
