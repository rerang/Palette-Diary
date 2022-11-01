#from array import array
from wordcloud import WordCloud
import matplotlib.pyplot as plt
import numpy as np
from PIL import Image
from PIL import *
import warnings
warnings.filterwarnings('ignore')

file = open('./KeywordData.txt','r', encoding='UTF-8')
x = file.read()
c=x.split()
print(c)

cc=[1]*len(c)
dict=dict(zip(c,cc))
print(dict)
file.close()

mask2=np.array(Image.open('tte2.jpg'))

#wc = WordCloud(font_path='./BMHANNA_11yrs_ttf.ttf', background_color='white', mask=mask2, colormap="Pastel1", width=400, height=400, scale=2.0, max_font_size=150)
wc = WordCloud(font_path='./BMHANNA_11yrs_ttf.ttf', background_color='white', colormap="Pastel1", width=400, height=400, scale=2.0, max_font_size=100)
gen = wc.generate_from_frequencies(dict)


plt.figure(figsize=(20,20))
plt.imshow(gen)
plt.axis('off')
#plt.show()
plt.savefig('KeywordWordcloud.png')
#wc.to_file('KeywordWordcloud.png')
