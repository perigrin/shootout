<p>Each program should
<ul>
  <li>read line-by-line a redirected <a href="http://en.wikipedia.org/wiki/Fasta_format">FASTA format</a> file from stdin</li>
  <li>extract DNA sequence THREE</li>

  <li>define a procedure/function to update a <strong>hashtable</strong> of k-nucleotide keys and count values, for a particular reading-frame &#8212; even though we'll combine k-nucleotide counts for all reading-frames</li>

  <li>write the code and percentage frequency, for <strong>all</strong> the 1-nucleotide and 2-nucleotide sequences, sorted by descending frequency and then ascending k-nucleotide key</li>
  <li>count <strong>all</strong> the 3- 4- 6- 12- and 18-nucleotide sequences, and write the count and code for specific sequences</li>
</ul>
</p>


<p>We use FASTA files generated by the <a href="benchmark.php?test=fasta&lang=all&sort=<?=$Sort;?>">fasta benchmark</a> as input for this benchmark. Note: the file may include both lowercase and uppercase codes.</p>

<p>Correct output for this 100KB <a href="iofile.php?test=<?=$SelectedTest;?>&lang=<?=$SelectedLang;?>&sort=<?=$Sort;?>&file=input">input file</a> (generated with the fasta program N = 10000), is
<pre>
A 30.28
T 29.80
C 20.31
G 19.61

AA 9.21
AT 8.95
TT 8.95
TA 8.94
CA 6.17
CT 6.10
AC 6.09
TC 6.04
AG 6.04
GA 5.97
TG 5.87
GT 5.80
CC 4.14
GC 4.04
CG 3.91
GG 3.80

562	GGT
152	GGTA
15	GGTATT
0	GGTATTTTAATT
0	GGTATTTTAATTTATAGT
</pre></p><br/>


<p>In practice, less brute-force would be used to calculate k-nucleotide frequencies, for example <a href="http://cbrg.inf.ethz.ch:8080/bio-recipes/VirusClassification/code.html">Virus Classification using k-nucleotide Frequencies</a> and <a href="http://www.hicomb.org/HiCOMB2002/papers/HICOMB2003-03.pdf">A Fast Algorithm for the Exhaustive Analysis of 12-Nucleotide-Long DNA Sequences. Applications to Human Genomics</a> (105KB pdf).


</p>

