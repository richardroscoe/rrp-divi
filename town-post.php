<?php 
	function get_services($num = 0)
	{
		// List of services offered
		$services = array(
			"Baby portraits",
			"<a href='http://newbornphotographyberkhamsted.co.uk/'>Newborn portraits</a>",
			"<a href='/experience/family-portrait-sessions/'>Family portraits</a>",
			"Toddler portraits",
			"Children's portraits",
			"Maternity portraits",
			"Baby passport photos",
			"Gift vouchers, Photography vouchers",
			"Wedding Anniversary portraits",
			"Kids portraits",
			"Canadian Passport Photos",
			"Children's Passport Photos",
			"USA Passport Photos",
			"Visa Passport Photos",
			"<a href='http://newbornphotographyberkhamsted.co.uk/'>Newborn baby portraits</a>",
			"<a href='/experience/babys-first-year/'>Baby's First Year Photography</a>",
			"<a href='/experience/babys-first-year/'>Cherubs</a>",
			"<a href='/experience/babys-first-year/'>Watch Me Grow</a>",
		);

		shuffle($services);		
		
		$i = 0;
		$out = '';
		foreach ($services as $s) {
			$out .= "<li>".$s."</li>";
			$i++;
			if ($i == $num)
				break;
		}
		return $out;
	}
	
	function get_body_text($town, $services)
	{
		$body_texts = array(
	
			//***************************************
	
			'<h2 class="town-post">%town% photographer</h2>
			<p>The studio provides portraiture services to clients from %town% and surrounding areas. Based in Berkhamsted, I specialise
			in contemporary portraits and in providing you with an enjoyable and fun photo shoot. </p>
			<ul>
			%services%
			</ul>
			<p>Please visit my online <a href="/gallery" title="Professional Photographer : Gallery" rel="nofollow">gallery</a>
			to see the style of work I have been creating recently.</p>
			<p>I\'m not on the high street which means I can give you<span class="special_offer"> excellent value</span>
			(check out the <a href="/prices" rel="nofollow">prices</a>) and still give you beautiful stunning photographs.</p>
			<p>Please give me a call or use the <a href="/contact" rel="nofollow">contact form</a> to send me a message.</p>',
			
			//***************************************
	
			'<h2 class="town-post">%town% photographer</h2>
			<p>At the Berkhamsted studio, I specialise in providing stunning portraits for families, parties, couples and
			individuals. Ensuring that a session at my studio is fun and enjoyable is a top priority for me. My work is
			characterised by beautiful contemporary photographs, an informal but professional approach and the highest
			level of customer service.</p>
			<ul>
			%services%
			</ul>
			<p>To see the style and quality of my work, please visit the <a href="/gallery" rel="nofollow">gallery</a>.</p>',
			
			//***************************************
			
			'<h2 class="town-post">%town% photographer %town% photographer %town% photographer</h2>
			<p><i>A Berkhamsted studio providing contemporary portraiture</i></p>
			<p><i>Worried about the prospect of visiting a photography studio?</i> Don\'t be - I\'ll make your visit fun,
			relaxed and enjoyable and at the end of it, you\'ll have some stunning pictures! Visit my
			<a href="/gallery" 	rel="nofollow">gallery</a> and see the style and quality of my work.<p>
			<p>If you would like to book a session or want to talk about a shoot, give me a call on <b>01442 870005</b></p>
			<ul>
			%services%
			</ul>
			',
			
		);
		shuffle($body_texts);
		return(str_replace(array('%town%', '%services%'), array($town, $services), $body_texts[0]));
	}

  $town_data = get_post_meta( get_the_ID(), '_town', true );
  $town = ( empty( $town_data['town_name'] ) ) ? '' : $town_data['town_name'];
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'town' ); ?>>
    <div class="entry-content">   	
        <h1><?php echo $town; ?> Portrait Photographer : Families, Children, Babies, Toddlers and Maternity</h1>
        <hr />
<?php echo get_body_text($town, get_services()); ?>

  </div>
</article>

