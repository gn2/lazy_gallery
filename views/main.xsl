<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0">


<xsl:output 
  method="html"
  doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
  doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" 
  indent="yes"/>



<xsl:template match="/">
  <html>
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <link rel="stylesheet" title="Style" type="text/css" href="stylesheets/main.css" />
      <title><xsl:value-of select="/page/config/pagetitle" /></title>

      <xsl:if test="/page/config/rsssupport = 1">
        <link href="index.php?rss" rel="alternate" type="application/rss+xml" />
      </xsl:if>
      <!-- Download SimpleViewer at www.airtightinteractive.com/simpleviewer -->
      <script type="text/javascript" src="simpleviewer/swfobject.js"></script>
      <style type="text/css">  
        /* hide from ie on mac \*/
        html {
          height: 100%;
          /*overflow: hidden;*/
        }
  
        #flashcontent {
          height: 100%;
        }
        /* end hide */

        body {
          height: 100%;
          margin: 0;
          padding: 0;
        }
      </style>
    </head>

    <body>
      
      <h1 id="title"><a href="index.php"><xsl:value-of select="/page/config/pagetitle" /></a>

      <xsl:if test="/page/config/rsssupport = 1">
        <a href="index.php?rss"><img id="rss-icon" src="images/rss.jpg" alt="Rss"/></a>
      </xsl:if>
      
      </h1>


      <xsl:if test="/page/config/somedirnotwritable = 'true'">
	      <p class="error">Some directories are not writable. Please fix it (see README file) as this is required to access any album.</p>
      </xsl:if>

      
      <!-- Display album list-->
      <xsl:for-each select="/page/albums/album">
        <table class="album">
          <tr>
          <td class="thumbnail">
            <xsl:if test="@thumbnail != ''">
		    <a href="index.php?album={.}"><img alt="thumbnail">
		      <xsl:attribute name="src">image.php?cache=true<xsl:text disable-output-escaping="yes">&amp;</xsl:text>album=<xsl:value-of select="." /><xsl:text disable-output-escaping="yes">&#38;</xsl:text>picture=<xsl:value-of select="@thumbnail" /></xsl:attribute>	      
                    </img></a>
            </xsl:if>
          </td>
          <td class="title">
            <h3><a href="index.php?album={.}"><xsl:value-of select="." /></a></h3>
            <xsl:if test="@count != ''">
              <small>[<xsl:value-of select="@count"/><xsl:value-of select="/page/config/pictures" />]</small>
            </xsl:if>
          </td>
          </tr>
        </table>
      </xsl:for-each>

      <xsl:if test="/page/config/totalPages">
	<br /><br />
        <xsl:call-template name="page.numbers">
          <xsl:with-param name="i">1</xsl:with-param>
          <xsl:with-param name="currentPage"><xsl:value-of select="/page/config/currentPage" /></xsl:with-param>
          <xsl:with-param name="total"><xsl:value-of select="/page/config/totalPages" /></xsl:with-param>
        </xsl:call-template>
        <br /><br />
      </xsl:if>

      <xsl:if test="/page/simpleviewer_album">
        <div id="flashcontent">SimpleViewer requires Macromedia Flash. <a href="http://www.macromedia.com/go/getflashplayer/">Get Macromedia Flash.</a> If you have Flash installed, <a href="index.php?detectflash=false">click to view gallery</a>.</div>
        <script type="text/javascript">
            var fo = new SWFObject("<xsl:value-of select="/page/config/swfdir" />viewer.swf", "viewer", "100%", "450px", "7", "#181818");  
            fo.addVariable("preloaderColor", "0xffffff");
            fo.addVariable("xmlDataPath", "<xsl:value-of select="/page/config/cachedir" /><xsl:value-of select="/page/simpleviewer_album/@xml_file" />");  
            fo.write("flashcontent");  
        </script>
      </xsl:if>

    </body>
  </html>
</xsl:template>




<!-- Define PageNumbers Element -->
<xsl:template name="page.numbers">

  <xsl:param name="i" />
  <xsl:param name="currentPage" />
  <xsl:param name="total"/>

  <!-- Output -->
  <xsl:if test="$i &lt;= $total">
    <xsl:if test="$i = $currentPage">
      <span class="currentpagenumber"><xsl:attribute name="href">index.php?page=<xsl:value-of select="$i" /></xsl:attribute><xsl:value-of select="$i" />  </span>
    </xsl:if>
    <xsl:if test="$i != $currentPage">
      <a class="pagenumber"><xsl:attribute name="href">index.php?page=<xsl:value-of select="$i" /></xsl:attribute><xsl:value-of select="$i" />  </a>
    </xsl:if>
  </xsl:if>

  <!--Repeat The Loop Until Finished-->
  <xsl:if test="$i &lt;= $total">
     <xsl:call-template name="page.numbers">
         <xsl:with-param name="i">
             <xsl:value-of select="$i + 1"/>
         </xsl:with-param>
         <xsl:with-param name="total">
             <xsl:value-of select="$total"/>
         </xsl:with-param>
         <xsl:with-param name="currentPage">
             <xsl:value-of select="$currentPage"/>
         </xsl:with-param>
     </xsl:call-template>
  </xsl:if>
</xsl:template>


</xsl:stylesheet>
