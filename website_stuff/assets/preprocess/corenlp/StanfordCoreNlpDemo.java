
import java.io.*;
import java.util.*;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.nio.charset.Charset;
import java.nio.charset.StandardCharsets;

import edu.stanford.nlp.dcoref.CorefChain;
import edu.stanford.nlp.dcoref.CorefCoreAnnotations;
import edu.stanford.nlp.io.*;
import edu.stanford.nlp.ling.*;
import edu.stanford.nlp.pipeline.*;
import edu.stanford.nlp.semgraph.SemanticGraph;
import edu.stanford.nlp.semgraph.SemanticGraphCoreAnnotations;
import edu.stanford.nlp.sentiment.SentimentCoreAnnotations;
import edu.stanford.nlp.trees.*;
import edu.stanford.nlp.util.*;

/** This class demonstrates building and using a Stanford CoreNLP pipeline. */
public class StanfordCoreNlpDemo {

	static class AnnotationClass{
		public boolean tokenize;
		public boolean sentSplit;
		public boolean posTag;
		public boolean lemmatize;
		public boolean nerTag;

		public AnnotationClass(){
			tokenize = false;
			sentSplit = false;
			posTag = false;
			lemmatize = false;
			nerTag = false;
		}
		public void setAnnotation(String annotation){
			if(annotation.equals("tokenize")){
				tokenize = true;
			}
			if(annotation.equals("sent_split")){
				sentSplit = true;
			}
			if(annotation.equals("pos_tag")){
				posTag = true;
			}
			if(annotation.equals("lemmatize")){
				lemmatize = true;
			}
			if(annotation.equals("ner_tag")){
				nerTag = true;
			}
		}
	}

	static String readFile(String path, Charset encoding) throws IOException{
		byte[] encoded = Files.readAllBytes(Paths.get(path));
		return new String(encoded, encoding);
	}

	/** Usage: java -cp "*" StanfordCoreNlpDemo [inputFile [outputTextFile [outputXmlFile]]] */
	public static void main(String[] args) throws IOException {

		PrintStream err = System.err;;
		System.setErr(new PrintStream(new OutputStream(){
			public void write(int b){
			}
		}));
		PrintStream out;
		out = new PrintStream(System.out);
		String fileIn = readFile(args[0], StandardCharsets.UTF_8);

		AnnotationClass activeAnnotations = new AnnotationClass();
		String annotations = "";
		for(String arg : args){
			if(arg.equals("tokenize")){
				annotations += "tokenize";
				annotations += ", ssplit";
			} 
			if(arg.equals("sent_split")){
				annotations += ", ssplit";
			} 
			if(arg.equals("pos_tag")){
				annotations += ", pos";
			} 
			if(arg.equals("lemmatize")){
				annotations += ", lemma";
			}
			if(arg.equals("ner_tag")){
				annotations += ", ner";
			}
			activeAnnotations.setAnnotation(arg);
		}

		// Create a CoreNLP pipeline. To build the default pipeline, you can just use:
		//   StanfordCoreNLP pipeline = new StanfordCoreNLP(props);
		// Here's a more complex setup example:
		//   Properties props = new Properties();
		//   props.put("annotators", "tokenize, ssplit, pos, lemma, ner, depparse");
		//   props.put("ner.model", "edu/stanford/nlp/models/ner/english.all.3class.distsim.crf.ser.gz");
		//   props.put("ner.applyNumericClassifiers", "false");
		//   StanfordCoreNLP pipeline = new StanfordCoreNLP(props);

		Properties props = new Properties();
		//props.put("annotators", "tokenize, ssplit, pos, lemma, ner, parse, dcoref, sentiment");
		props.put("annotators", annotations);

		StanfordCoreNLP pipeline = new StanfordCoreNLP(props);

		Annotation document = new Annotation(fileIn);

		pipeline.annotate(document);
		List<CoreMap> sentences = document.get(CoreAnnotations.SentencesAnnotation.class);
		if(sentences == null){
			out.println("NULL");
		}

		if(activeAnnotations.nerTag == true){
			for(CoreMap sentence : sentences){
				for (CoreMap token : sentence.get(CoreAnnotations.TokensAnnotation.class)) {
					out.print(token.get(CoreAnnotations.TextAnnotation.class) + 
							"/" + token.get(CoreAnnotations.PartOfSpeechAnnotation.class) + 
							"/" + token.get(CoreAnnotations.NamedEntityTagAnnotation.class) + " ");
				}
			}
		}

		else if(activeAnnotations.lemmatize == true){
			for(CoreMap sentence : sentences){
				for (CoreMap token : sentence.get(CoreAnnotations.TokensAnnotation.class)) {
					out.print(token.get(CoreAnnotations.TextAnnotation.class) + 
							"/" + token.get(CoreAnnotations.PartOfSpeechAnnotation.class) + 
							"/" + token.get(CoreAnnotations.LemmaAnnotation.class) + " ");
				}
			}
		}

		else if(activeAnnotations.posTag == true){
			for(CoreMap sentence : sentences){
				for (CoreMap token : sentence.get(CoreAnnotations.TokensAnnotation.class)) {
					out.print(token.get(CoreAnnotations.TextAnnotation.class) + 
							"/" + token.get(CoreAnnotations.PartOfSpeechAnnotation.class) + " ");
				}
			}
		}

		else if(activeAnnotations.sentSplit == true){
			for(CoreMap sentence : sentences){
				out.println(sentence.get(CoreAnnotations.TextAnnotation.class));
			}
		}

		else if(activeAnnotations.tokenize == true){
			for(CoreMap sentence : sentences){
				for (CoreMap token : sentence.get(CoreAnnotations.TokensAnnotation.class)) {
					out.println(token.get(CoreAnnotations.TextAnnotation.class));
				}
			}
		}

		// this prints out the results of sentence analysis to file(s) in good formats
		/*
			 pipeline.prettyPrint(annotation, out);
			 if (xmlOut != null) {
			 pipeline.xmlPrint(annotation, xmlOut);
			 }
			 */

		// Access the Annotation in code
		// The toString() method on an Annotation just prints the text of the Annotation
		// But you can see what is in it with other methods like toShorterString()
		/*
			 out.println();
			 out.println("The top level annotation");
			 out.println(annotation.toShorterString());
			 out.println();
			 */

		// An Annotation is a Map with Class keys for the linguistic analysis types.
		// You can get and use the various analyses individually.
		// For instance, this gets the parse tree of the first sentence in the text.
		/*
			 List<CoreMap> sentences = annotation.get(CoreAnnotations.SentencesAnnotation.class);
			 if (sentences != null && ! sentences.isEmpty()) {
			 CoreMap sentence = sentences.get(0);
			 out.println("The keys of the first sentence's CoreMap are:");
			 out.println(sentence.keySet());
			 out.println();
			 out.println("The first sentence is:");
			 out.println(sentence.toShorterString());
			 out.println();
			 out.println("The first sentence tokens are:");
			 for (CoreMap token : sentence.get(CoreAnnotations.TokensAnnotation.class)) {
			 out.println(token.toShorterString());
			 }
			 Tree tree = sentence.get(TreeCoreAnnotations.TreeAnnotation.class);
			 out.println();
			 out.println("The first sentence parse tree is:");
			 tree.pennPrint(out);
			 out.println();
			 out.println("The first sentence basic dependencies are:");
			 out.println(sentence.get(SemanticGraphCoreAnnotations.BasicDependenciesAnnotation.class).toString(SemanticGraph.OutputFormat.LIST));
			 out.println("The first sentence collapsed, CC-processed dependencies are:");
			 SemanticGraph graph = sentence.get(SemanticGraphCoreAnnotations.CollapsedCCProcessedDependenciesAnnotation.class);
			 out.println(graph.toString(SemanticGraph.OutputFormat.LIST));

		// Access coreference. In the coreference link graph,
		// each chain stores a set of mentions that co-refer with each other,
		// along with a method for getting the most representative mention.
		// Both sentence and token offsets start at 1!
		out.println("Coreference information");
		Map<Integer, CorefChain> corefChains =
		annotation.get(CorefCoreAnnotations.CorefChainAnnotation.class);
		if (corefChains == null) { return; }
		for (Map.Entry<Integer,CorefChain> entry: corefChains.entrySet()) {
		out.println("Chain " + entry.getKey() + " ");
		for (CorefChain.CorefMention m : entry.getValue().getMentionsInTextualOrder()) {
		// We need to subtract one since the indices count from 1 but the Lists start from 0
		List<CoreLabel> tokens = sentences.get(m.sentNum - 1).get(CoreAnnotations.TokensAnnotation.class);
		// We subtract two for end: one for 0-based indexing, and one because we want last token of mention not one following.
		out.println("  " + m + ", i.e., 0-based character offsets [" + tokens.get(m.startIndex - 1).beginPosition() +
		", " + tokens.get(m.endIndex - 2).endPosition() + ")");
		}
		}
		out.println();

		out.println("The first sentence overall sentiment rating is " + sentence.get(SentimentCoreAnnotations.SentimentClass.class));
			 }
			 IOUtils.closeIgnoringExceptions(out);
			 IOUtils.closeIgnoringExceptions(xmlOut);
	}
	*/
		System.setErr(err);
	}
}
