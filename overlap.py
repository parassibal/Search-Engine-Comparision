from collections import defaultdict
filePath = "links.csv"
dc = {}
count = defaultdict(int)
overlap = defaultdict(int)
with open(filePath, "r") as file:
    for line in file:
        record = line.split(',')
        query, lucene, pagerank = record[0], record[1], record[2] 
        if query == "\ufeff":
            continue
        count[query] += 1
        if query not in dc:
            dc[query] = {"lucene": [lucene], "pagerank":[pagerank]}
        else:
            dc[query]["lucene"].append(lucene)
            dc[query]["pagerank"].append(pagerank)
    for query in dc:
        lucene_links = dc[query]["lucene"]
        pagerank_links = dc[query]["pagerank"]
        c = 0
        for ll in lucene_links:
            for pl in pagerank_links:
                if ll.strip() == pl.strip():
                    c += 1
                    print(ll, "----", pl)
        overlap[query] = c
print(count)
print(overlap)
