import os

def refreshIndex(currentpath,linkprefix):
	outdated=0
	if not os.path.exists(currentpath+'/index.html'):
		outdated=1
	else:
		#print os.path.getmtime(currentpath)
		#print os.path.getmtime(currentpath+'/index.html')
		if os.path.getmtime(currentpath)>os.path.getmtime(currentpath+'/index.html'):
			outdated=1
			
	#outdated=1
	if outdated:
		(head,tail)=os.path.split(os.getcwd()+'/'+currentpath)
		s='Directorylisting of '+tail+'<br><br>'
		d=[]
		for filename in os.listdir(currentpath):
				if os.path.isdir(os.path.join(currentpath, filename)):
					s=s+'<a href="'+linkprefix+currentpath+'/'+filename+'/index.html">'+filename+'/</a><br>'
					d.append(filename)
		s=s+'<br>'
		for filename in os.listdir(currentpath):
				if os.path.isfile(os.path.join(currentpath, filename)):
					if not filename.startswith('.') ^ filename.startswith('index.html') ^ filename.startswith('Icon'):
						s=s+'<a href="'+linkprefix+currentpath+'/'+filename+'">'+filename+'</a><br>'
		#print d
		
		f=open(currentpath+'/index.html',"w")
		f.write(s)
		f.close()
		
		for dirname in d:
			refreshIndex(currentpath+'/'+dirname,linkprefix)
		
	
if __name__ == "__main__":
	refreshIndex('.','https://dl.dropbox.com/u/2847942/')
