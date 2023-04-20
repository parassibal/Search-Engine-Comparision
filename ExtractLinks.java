import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Set;
import java.io.FileWriter;

public class ExtractLinks 
{
	public static void main(String[] args) throws Exception
	{
		HashMap<String, String> urlFileMap = new HashMap<>();
		HashMap<String, String> fileUrlMap = new HashMap<>();
		String rootPath = "/Users/paras/Desktop/solr-7.7.3/LATIMES";
		@SuppressWarnings("resource")
		BufferedReader fileReader = new BufferedReader(new FileReader(rootPath + "/URLtoHTML_latimes_news.csv"));
		String line = "";
	
		while((line = fileReader.readLine()) != null)
		{
			String[] tokens = line.split(",");
			String fileName = tokens[0];
			String URL = tokens[1];
			urlFileMap.put(URL, fileName);
			fileUrlMap.put(fileName, URL);
		}

		Set<String> edges = new HashSet<String>();
		File dir = new File(rootPath + "/latimes");
		for(File file: dir.listFiles())
		{
			try {
				Document doc = Jsoup.parse(file, "UTF-8", fileUrlMap.get(file.getName()));
				
				Elements links = doc.select("a[href]");
				for (Element link : links) {
					String url = link.attr("abs:href").trim();
					
					if (urlFileMap.containsKey(url)) {
						edges.add(file.getName() + " " + urlFileMap.get(url));
					}
				}
			}
			catch(Exception ex)
			{
				System.out.println(file.getName());
			}
		}

		FileWriter writer = new FileWriter("edgelist.txt");
		
		for(String s: edges) 
		{
			writer.write(s + "\n");
		}
		writer.flush();
		writer.close();
	}
}