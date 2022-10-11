from array import array
from wordCloud import WordCloud
import matplotlib.pyplot as plt

file = open('./KeywordData.txt','r', encoding='UTF-8')
x = file.read()
c=x.split()
print(c)
  
cc=[1]*len(c)
dict=dict(zip(c,cc))
#print(dict)
file.close()

#MalangmalangR 폰트 없으면 다른 폰트로 교체, 팔레트 색상도 고려중
wc = WordCloud(font_path='../../../resources/malgun.ttf', background_color='white', colormap="Pastel1", width=400, height=400, scale=2.0, max_font_size=150)
gen = wc.generate_from_frequencies(dict)


plt.figure(figsize=(20,20))
plt.imshow(gen)
plt.axis('off')
#plt.show()
plt.savefig('KeywordWordcloud.png')
#wc.to_file('KeywordWordcloud.png')