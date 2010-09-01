<?php 
/*
  Test Expectations:
  - For all run in tests, run in length is 6 words

  Test Series:
   - Drop cap only, no run in
   - Drop cap + run in
   - Run in only
*/

$cases = array();

// Plain paragraph
$cases[] = <<<CASE
WordPress is web software you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.
CASE;

// Paragraph with <p> tags already
$cases[] = <<<CASE
<p>WordPress is web software you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.</p>
CASE;


// Paragraph with <p class="xxx"> tag
$cases[] = <<<CASE
<p class="intro first lead">WordPress is web software you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.</p>
CASE;


// Paragraph with <x> tags inside text run range
$cases[] = <<<CASE
<p>WordPress is <em>web software</em> you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.</p>
CASE;


// Paragraph with <x> tags that run from inside to outside text run range
$cases[] = <<<CASE
<p>WordPress is web software <em>you can use</em> to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.</p>
CASE;


// Paragraph with multiple <x> tag spans inside text run range
$cases[] = <<<CASE
<p><strong>WordPress</strong> is <em>web software</em> <u>you</u> can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.</p>
CASE;


// Paragraph with multiple <x> tag spans with last running outside text run range
$cases[] = <<<CASE
<p><strong>WordPress</strong> is <em>web software</em> <em>you can use</em> to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.</p>
CASE;


// Paragraph with triple-level tags, some inside, some outside text run range
$cases[] = <<<CASE
<p>WordPress is <em><strong>web software <u>you</u></strong> can use to create a beautiful website or blog</em>. We like to say that WordPress is both free and priceless at the same time.</p>
CASE;


// Paragraph with unbalanced / non-validating HTML tags


// Paragraph with tags starting before the drop cap


// <x> tags that don't end on word boundaries
$cases[] = <<<CASE
<p>This is un<em>fucking</em>believable. What do we do now?</p>
CASE;


// Tags with attributes


// Tags with attributes containing ">" character


// Paragraph with multibyte characters (UTF8)


// Paragraph shorter (in words) than the run in length


// Hard-wrapped paragraphs


// Kitchen sink, From DIYThemes
$cases[] = <<<CASE
<p>Simply put, Thesis is <em>powerful</em>. It has a remarkably efficient <acronym title="HyperText Markup Language">HTML</acronym> <code>+</code> <acronym title="Cascading Style Sheet">CSS</acronym> <code>+</code> <acronym title="recursive acronym for Hypertext Preprocessor">PHP</acronym> framework and easy-to-use controls that you can use to fine-tune each and every page of your site with a tactical precision that has never been possible before. The days of worrying about your in-site <acronym title="Search Engine Optimization">SEO</acronym> are over—with Thesis, your strategy is “just add killer content.”</p>
CASE;


/* 
// Lorem Ipsum
Apple ignited the personal computer revolution in the 1970s with the Apple II and reinvented the personal computer in the 1980s with the Macintosh. Today, Apple continues to lead the industry in innovation with its award-winning computers, OS X operating system and iLife and professional applications. Apple is also spearheading the digital media revolution with its iPod portable music and video players and iTunes online store, and has entered the mobile phone market with its revolutionary iPhone.

We the People of the United States, in Order to form a more perfect Union, establish Justice, ensure domestic Tranquility, provide for the common defence, promote the general Welfare, and secure the Blessings of Liberty to ourselves and our Posterity, do ordain and establish this Constitution for the United States of America.

Four score and seven years ago our fathers brought forth 
on this continent a new nation, conceived in liberty, and 
dedicated to the proposition that all men are created equal.

Now we are engaged in a great civil war, testing whether that 
nation, or any nation, so conceived and so dedicated, can long 
endure. We are met on a great battle-field of that war. We 
have come to dedicate a portion of that field, as a final 
resting place for those who here gave their lives that that 
nation might live. It is altogether fitting and proper that we 
should do this.

But, in a larger sense, we can not dedicate, we can not consecrate, we can not hallow this ground. The brave men, living and dead, who struggled here, have consecrated it, far above our poor power to add or detract. The world will little note, nor long remember what we say here, but it can never forget what they did here. It is for us the living, rather, to be dedicated here to the unfinished work which they who fought here have thus far so nobly advanced. It is rather for us to be here dedicated to the great task remaining before us—that from these honored dead we take increased devotion to that cause for which they gave the last full measure of devotion—that we here highly resolve that these dead shall not have died in vain—that this nation, under God, shall have a new birth of freedom—and that government of the people, by the people, for the people, shall not perish from the earth.
*/
?>