<?php


namespace App\Tests;


use App\Service\TextManipulationService;
use PHPUnit\Framework\TestCase;

class TextManipulationTest extends TestCase
{

    public function testGetArrayOfWordsFromString(){

        $sting = 'Microsoft flung out fresh updates to its cross-platform data wrangler Azure Data Studio, the Windows Terminal and continued to tease a standalone Windows Subsystem for Linux last night. The open-source Azure Data Studio gained better sign-in options for Azure Data Services – handy because, well, the hint is in the name of the product (although Microsoft does seem to have a penchant for slapping the "Azure" branding on pretty much everything these days). Faffing around with device codes was required in earlier versions for Azue connections, which wasn\'t particularly convenient. The latest take, while still not as simple as signing into an on-premises SQL instance, improves things by dropping the user into a browser to log into an Azure account. Closing the window then leaves Azure Data Studio hooked up. It is an easier process, for sure, but still a little jarring. From the department of "I thought it did that already" comes the ability to use a common pattern to find text and code within a notebook, making things a little more consistent with how finding within an editor works. The release also includes the version 1.42 updates from Visual Code. Version 1.15 can be downloaded for Windows, macOS and Linux. Or just do what we did and let the thing update itself. While still not quite a replacement for the venerable SQL Server Management Studio (for SQL fans at least), the updating of the Azure integration certainly made our lives easier even if this hack is still struggling to shed the CTRL+E muscle memory for executing a highlighted query.';

        $manipulation = new TextManipulationService();

        $list = $manipulation->getArrayOfWordsFromString($sting);

        $result = $manipulation->countWordsInArray($list);

        $this->assertEquals(7,$result['azure']);

    }


}