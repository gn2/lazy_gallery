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
    </head>

    <body>
      
      <h1 id="title"><a href="index.php"><xsl:value-of select="/page/config/pagetitle" /></a></h1>

      <form action="login.php" method="POST">
	      <label><xsl:value-of select="/page/config/password" /> : </label>
	      <input type="password" value="" name="password"/>
	      <input type="submit" value="Go!"/>
      

      </form>
            
    </body>
  </html>
</xsl:template>

</xsl:stylesheet>
