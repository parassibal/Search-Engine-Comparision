import networkx as nx
graph = nx.read_edgelist("edges.txt")
page_rank = nx.pagerank(graph)
with open("external_pageRankFile.txt", "w") as f:
    for key, value in page_rank.items():
        pass
        #f.write(f"/Users/paras/Desktop/result/{key}={value}\n")#


