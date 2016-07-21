import os
import sys
import csv
import Image
import PIL

sourceImageLocation = '/home/soda/DropboxBibsmart/Dropbox/Shared/Images/MasterMarathonImages/Caroline/Indianapolis-Monumental-Mile-Combined/'
fileType = ''
inputFileName = ''
outputDir = ''

if (len(sys.argv) <= 1):
    print("Need to enter an crop file as first argument. For example: python cropPatches.py -short cropFile.csv outputDirectory")
    sys.exit()
elif (len(sys.argv) <= 2):
    print("Need to enter an output directory. For example: python cropPatches.py -short cropFile.csv outputDirectory")
    sys.exit()
elif (len(sys.argv) == 3):
    fileType = '-short'
    inputFileName = sys.argv[1]
    outputDir = sys.argv[2]
elif (len(sys.argv) == 4):
    fileType = sys.argv[1]
    inputFileName = sys.argv[2]
    outputDir = sys.argv[3]
    
if (fileType != "-short" and fileType != "-long"):
    print("Need to enter a filetype of '-short' for short csv or '-long' for long csv. For example: python cropPatches.py -short cropFile.csv outputDirectory")
    sys.exit()
    

if not os.path.exists(outputDir):
        os.makedirs(outputDir)

with open(inputFileName, 'rb') as f:
    reader = csv.reader(f)
    resultList = list(reader)

indexAdjust = 0;
if (fileType == "-long"):
    print("Cropping for long CSV....")
    indexAdjust = 8

imageCount = {}
for i in range(len(resultList)):
    imageFile = resultList[i][0].strip()
    label = resultList[i][1]
    patchX1 = int(resultList[i][2 + indexAdjust])
    patchY1 = int(resultList[i][3 + indexAdjust])
    patchX2 = int(resultList[i][4 + indexAdjust])
    patchY2 = int(resultList[i][5 + indexAdjust])
    #patchWidth = patchX2 - patchX1
    #patchHeight = patchY2 - patchY1
    imageTargetWidth = int(resultList[i][6 + indexAdjust])
    imageTargetHeight = int(resultList[i][7 + indexAdjust])
    
    recognizedLabel = 0
    if (fileType == "-long"):
        recognizedLabel = resultList[i][17]
    
    if (fileType != "-long" or (fileType == "-long" and label == "-1")):
        image = Image.open(sourceImageLocation + imageFile)
    
        if (image.size[0] != imageTargetWidth and image.size[1] != imageTargetHeight):
            #print("Resizing: Width {0} Height {1}".format(image.size[0], image.size[1]))
            image = image.resize((imageTargetWidth,imageTargetHeight), PIL.Image.ANTIALIAS)
            
        # crop the image
        if (imageFile in imageCount):
            imageCount[imageFile] = imageCount[imageFile] + 1
        else:
            imageCount[imageFile] = 1
        
        imageFileWithoutExt = os.path.splitext(imageFile)[0]
        finalPatchPath = outputDir + "/" + imageFileWithoutExt + "-newpatch" + str(imageCount[imageFile]) + ".jpg"
        image.crop((patchX1, patchY1, patchX2, patchY2)).save(finalPatchPath)
        print("Created patch: " + finalPatchPath)
        
    