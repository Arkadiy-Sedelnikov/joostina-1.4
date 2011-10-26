<?php
/**
* ImageManager Class.
* @author $Author: Ryan Demmer $
* @version $Id: manager.class.php 27 2005-09-14 17:51:00 Ryan Demmer $
*/
class imageManager extends Manager
{
        /**
       * Constructor. Create a new Manager instance.e
       */
        function imageManager( $base_dir, $base_url )
        {
                $this->base_dir = $base_dir;
                $this->base_url = $base_url;
        }
		function getProperties( $file )
		{
			global $jce;
			clearstatcache();
			
			$path = JPath::makePath( $this->getBaseDir(), urldecode( $file ) );
			$url = JPath::makePath( $this->getBaseUrl(), urldecode( $file ) );
			$ext = JFile::getExt( $path );
			$dim = @getimagesize( $path );
			
			$date = JCEUtils::formatDate( filemtime( $path ) );
            $size = JCEUtils::formatSize( filesize( $path ) );
			
			$pw = ( $dim[0] >= 120 ) ? 120 : $dim[0];
            $ph = ( $pw / $dim[0] )* $dim[1];
			
			if( $ph > 120 ){
				$ph = 120;
          		$pw = ( $ph / $dim[1] )* $dim[0];
			}
								
			$html = '<div>' . $jce->translate('dimensions') . ': ' . $dim[0] . ' x ' . $dim[1] . '</div>';
			$html .= '<div>' . $jce->translate('size') . ': ' . $size . '</div>';
			$html .= '<div>' . $jce->translate('modified') . ': ' . $date . '</div>';
			$html .= '<div style="text-align:center; margin-top:5px;"><img src="' . $url . '" width="' . $pw . '" height="' . $ph . '"/></div>';

			return "<script>showProperties('" . $jce->ajaxHTML( $html ) . "','" . $dim[0] . "','" . $dim[1] . "');</script>";
		}
		function getDimensions( $file )
		{		
			$path = JPath::makePath( $this->getBaseDir(), urldecode( $file ) );
			$dim = @getimagesize( $path );
			
			return "<script>getDimensions('" . $dim[0] . "','" . $dim[1] . "');</script>";
		}
}

?>
