import os
import csv
rootdir = '/home/soda/Downloads/patches-Indianapolis-Monumental-Mile-Combined-2'
outputDir = '/home/soda/Downloads/patches-Indianapolis-Monumental-Mile-Combined-2/patchFiles.csv'

def CreateLabelCSV(csvData, csvFileName):
    print("Creating csv file...".format(csvFileName))
    with open(csvFileName, 'wb') as csvFile:
        for x in xrange(len(csvData)):
            csvFile.writelines(csvData[x] + "\n")

fileList = list()
for subdir, dirs, files in os.walk(rootdir):
    for file in files:
        fileString = "{0},{1}".format(file, subdir)
        fileList.append(fileString)

print("Saving to {0}".format(outputDir))
CreateLabelCSV(fileList, outputDir)
        
